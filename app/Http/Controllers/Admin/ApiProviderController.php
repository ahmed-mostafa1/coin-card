<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiProvider;
use App\Services\ApiProviderManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class ApiProviderController extends Controller
{
    public function __construct(
        private readonly ApiProviderManager $manager
    ) {}

    public function index(): View
    {
        $providers = ApiProvider::withCount('services')->latest()->get();
        return view('admin.providers.index', compact('providers'));
    }

    public function create(): View
    {
        return view('admin.providers.form', [
            'provider'  => null,
            'authTypes' => ApiProvider::AUTH_TYPES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        ApiProvider::create([
            ...$data['base'],
            'credentials' => $data['credentials'],
        ]);

        return redirect()->route('admin.providers.index')
            ->with('success', 'تم إضافة المزود بنجاح.');
    }

    public function edit(ApiProvider $provider): View
    {
        return view('admin.providers.form', [
            'provider'  => $provider,
            'authTypes' => ApiProvider::AUTH_TYPES,
        ]);
    }

    public function update(Request $request, ApiProvider $provider): RedirectResponse
    {
        $data = $this->validated($request, $provider);

        $provider->update([
            ...$data['base'],
            'credentials' => $data['credentials'],
        ]);

        return redirect()->route('admin.providers.index')
            ->with('success', 'تم تحديث المزود بنجاح.');
    }

    public function destroy(ApiProvider $provider): RedirectResponse
    {
        if ($provider->services()->exists()) {
            return back()->with('error', 'لا يمكن حذف المزود لأن هناك خدمات مرتبطة به.');
        }

        $provider->delete();

        return redirect()->route('admin.providers.index')
            ->with('success', 'تم حذف المزود.');
    }

    /**
     * Test connection + return first 3 mapped products as preview (AJAX).
     */
    public function testConnection(Request $request, ApiProvider $provider): JsonResponse
    {
        try {
            $catalogService = $this->manager->forProvider($provider);
            $result = $catalogService->browse(['page' => 1]);

            if (! $result['ok']) {
                return response()->json([
                    'ok'      => false,
                    'message' => $result['error_message'],
                ]);
            }

            return response()->json([
                'ok'       => true,
                'count'    => $result['count'],
                'preview'  => array_slice($result['products'], 0, 3),
                'message'  => 'الاتصال ناجح. عدد المنتجات: ' . $result['count'],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok'      => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function validated(Request $request, ?ApiProvider $provider = null): array
    {
        $request->validate([
            'name'                    => ['required', 'string', 'max:255'],
            'slug'                    => ['required', 'string', 'max:100', 'alpha_dash',
                Rule::unique('api_providers', 'slug')->ignore($provider?->id)],
            'is_active'               => ['boolean'],
            'notes'                   => ['nullable', 'string'],
            // Auth
            'auth_type'               => ['required', Rule::in(array_keys(ApiProvider::AUTH_TYPES))],
            'timeout'                 => ['integer', 'min:5', 'max:120'],
            // Credentials (conditionally required by auth_type)
            'cred_key'                => ['nullable', 'string'],
            'cred_secret'             => ['nullable', 'string'],
            'cred_username'           => ['nullable', 'string'],
            'cred_password'           => ['nullable', 'string'],
            'cred_token'              => ['nullable', 'string'],
            // Catalog
            'base_url'                => ['required', 'url'],
            'catalog_endpoint'        => ['required', 'string'],
            'catalog_method'          => ['required', Rule::in(['GET', 'POST'])],
            'catalog_response_path'   => ['nullable', 'string'],
            'catalog_count_path'      => ['nullable', 'string'],
            'catalog_next_path'       => ['nullable', 'string'],
            'catalog_pagination_type' => ['required', Rule::in(['none', 'page_number', 'offset'])],
            'catalog_page_param'      => ['nullable', 'string'],
            'catalog_page_size_param' => ['nullable', 'string'],
            'catalog_page_size'       => ['integer', 'min:1', 'max:1000'],
            // Field map
            'field_map_name'          => ['required', 'string'],
            'field_map_price'         => ['required', 'string'],
            'field_map_external_id'   => ['nullable', 'string'],
            'field_map_description'   => ['nullable', 'string'],
            'field_map_type'          => ['nullable', 'string'],
            'field_map_available'     => ['nullable', 'string'],
            // Order placement
            'order_endpoint'          => ['nullable', 'string'],
            'order_method'            => ['nullable', Rule::in(['POST', 'GET'])],
            'order_our_ref_field'     => ['nullable', 'string'],
            'order_product_field'     => ['nullable', 'string'],
            'order_account_id_field'  => ['nullable', 'string'],
            'order_account_id_source' => ['nullable', 'string'],
            'order_quantity_field'    => ['nullable', 'string'],
            'order_quantity_source'   => ['nullable', 'string'],
            'order_extra_fields'      => ['nullable', 'json'],
            'order_transaction_id_path' => ['nullable', 'string'],
            'order_exec_status_path'  => ['nullable', 'string'],
            // Order status
            'status_endpoint'         => ['nullable', 'string'],
            'status_method'           => ['nullable', Rule::in(['GET', 'POST'])],
            'status_id_param'         => ['nullable', 'string'],
            'status_exec_path'        => ['nullable', 'string'],
            'status_success_values'   => ['nullable', 'string'],
            'status_failed_values'    => ['nullable', 'string'],
        ]);

        $credentials = match ($request->auth_type) {
            ApiProvider::AUTH_API_KEY_HEADER,
            ApiProvider::AUTH_API_KEY_QUERY  => ['key' => $request->cred_key, 'secret' => $request->cred_secret],
            ApiProvider::AUTH_BASIC_HEADER,
            ApiProvider::AUTH_BASIC_QUERY    => ['username' => $request->cred_username, 'password' => $request->cred_password],
            ApiProvider::AUTH_BEARER_HEADER  => ['token' => $request->cred_token],
            default                          => [],
        };

        // If editing and a credential field is empty, keep the old value
        if ($provider) {
            foreach ($credentials as $key => $value) {
                if (blank($value)) {
                    $credentials[$key] = $provider->getCredential($key);
                }
            }
        }

        $base = $request->only([
            'name', 'slug', 'notes',
            'auth_type', 'timeout',
            'base_url', 'catalog_endpoint', 'catalog_method',
            'catalog_response_path', 'catalog_count_path', 'catalog_next_path',
            'catalog_pagination_type', 'catalog_page_param', 'catalog_page_size_param', 'catalog_page_size',
            'field_map_name', 'field_map_price', 'field_map_external_id',
            'field_map_description', 'field_map_type', 'field_map_available',
            'order_endpoint', 'order_method', 'order_our_ref_field',
            'order_product_field', 'order_account_id_field', 'order_account_id_source',
            'order_quantity_field', 'order_quantity_source', 'order_extra_fields',
            'order_transaction_id_path', 'order_exec_status_path',
            'status_endpoint', 'status_method', 'status_id_param', 'status_exec_path',
        ]);

        $base['is_active'] = $request->boolean('is_active');

        // Convert comma-separated status values to JSON arrays
        $base['status_success_values'] = $this->parseValues($request->input('status_success_values'));
        $base['status_failed_values']  = $this->parseValues($request->input('status_failed_values'));

        return ['base' => $base, 'credentials' => $credentials];
    }

    private function parseValues(?string $input): ?array
    {
        if (blank($input)) {
            return null;
        }
        return array_values(array_filter(array_map('trim', explode(',', $input))));
    }
}
