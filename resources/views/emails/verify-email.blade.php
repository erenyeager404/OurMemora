<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Verifikasi Email — OurMemora</title>
</head>

<body style="margin:0;padding:0;background:#0F172A;font-family:Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 20px;">
    <tr>
      <td align="center">
        <table width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;">

          {{-- Logo --}}
          <tr>
            <td align="center" style="padding-bottom:24px;">
              <span style="font-size:28px;font-weight:800;color:#FFFFFF;">
                Our<span style="color:#A78BFA;">Memora</span>
              </span>
            </td>
          </tr>

          {{-- Card --}}
          <tr>
            <td style="background:#1E293B;border:1px solid #334155;border-radius:16px;overflow:hidden;">
              <table width="100%" cellpadding="0" cellspacing="0">

                {{-- Banner --}}
                <tr>
                  <td align="center"
                    style="background:linear-gradient(135deg,#4C1D95,#7C3AED);padding:40px 32px;border-radius:16px 16px 0 0;">
                    <div style="font-size:40px;margin-bottom:12px;">✉</div>
                    <div style="font-size:22px;font-weight:700;color:#FFFFFF;margin-bottom:6px;">Verifikasi Email Kamu
                    </div>
                    <div style="font-size:14px;color:#C4B5FD;">Satu langkah untuk mengabadikan momen</div>
                  </td>
                </tr>

                {{-- Body --}}
                <tr>
                  <td style="padding:32px;">
                    <p style="font-size:16px;color:#CBD5E1;margin:0 0 12px 0;">
                      Halo, <strong style="color:#F1F5F9;">{{ $user->name }}</strong>!
                    </p>
                    <p style="font-size:14px;color:#94A3B8;line-height:1.7;margin:0 0 28px 0;">
                      Selamat datang di <strong style="color:#A78BFA;">OurMemora</strong>.
                      Klik tombol di bawah untuk memverifikasi email kamu dan mulai mengabadikan kenangan.
                    </p>

                    <table width="100%" cellpadding="0" cellspacing="0">
                      <tr>
                        <td align="center" style="padding-bottom:28px;">
                          <a href="{{ $url }}"
                            style="display:inline-block;background:linear-gradient(135deg,#6D28D9,#7C3AED);color:#FFFFFF;text-decoration:none;font-size:15px;font-weight:700;padding:14px 44px;border-radius:10px;">
                            ✓ Verifikasi Email Sekarang
                          </a>
                        </td>
                      </tr>
                    </table>

                    <div
                      style="background:#1E1A2E;border:1px solid #4C1D95;border-radius:10px;padding:12px 16px;font-size:12px;color:#A78BFA;margin-bottom:20px;">
                      ⏰ Link kadaluarsa dalam <strong>60 menit</strong>.
                    </div>

                    <hr style="border:none;border-top:1px solid #334155;margin:20px 0;">

                    <p style="font-size:12px;color:#64748B;margin:0 0 6px 0;">
                      Jika tombol tidak berfungsi, copy URL ini ke browser:
                    </p>
                    <p style="font-size:11px;margin:0;word-break:break-all;">
                      <a href="{{ $url }}" style="color:#818CF8;">{{ $url }}</a>
                    </p>

                    <hr style="border:none;border-top:1px solid #334155;margin:20px 0;">

                    <p style="font-size:12px;color:#64748B;margin:0;">
                      Jika tidak mendaftar, abaikan email ini.
                    </p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          {{-- Footer --}}
          <tr>
            <td align="center" style="padding-top:20px;">
              <p style="font-size:12px;color:#475569;margin:0;line-height:1.8;">
                © {{ date('Y') }} OurMemora — Email otomatis, jangan dibalas.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>

</html>