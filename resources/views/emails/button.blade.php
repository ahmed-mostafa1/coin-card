@props(['url', 'text'])

<table style="width: 100%;" cellpadding="0" cellspacing="0" role="presentation">
  <tr>
    <td align="center" style="padding: 24px 0;">
      <!--[if mso]>
      <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ $url }}" style="height:48px;v-text-anchor:middle;width:200px;" arcsize="12%" strokecolor="#111827" fillcolor="#111827">
        <w:anchorlock/>
        <center style="color:#ffffff;font-family:sans-serif;font-size:16px;font-weight:600;">{{ $text }}</center>
      </v:roundrect>
      <![endif]-->
      <!--[if !mso]>-->
      <a href="{{ $url }}"
         style="display: inline-block;
                background-color: #111827;
                color: #ffffff;
                font-size: 16px;
                font-weight: 600;
                line-height: 48px;
                text-align: center;
                text-decoration: none;
                width: 200px;
                border-radius: 8px;">{{ $text }}</a>
      <!--<![endif]-->
    </td>
  </tr>
</table>