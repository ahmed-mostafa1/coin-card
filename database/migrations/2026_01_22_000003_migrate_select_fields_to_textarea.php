<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $selectFieldIds = DB::table('service_form_fields')
            ->where('type', 'select')
            ->pluck('id');

        if ($selectFieldIds->isNotEmpty()) {
            DB::table('service_form_options')
                ->whereIn('field_id', $selectFieldIds->all())
                ->delete();

            DB::table('service_form_fields')
                ->whereIn('id', $selectFieldIds->all())
                ->update(['type' => 'textarea']);
        }
    }

    public function down(): void
    {
        // No reverse migration for deleted options.
    }
};
