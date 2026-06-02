```html
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Verifikasi Email - OurMemora</title>
</head>

<body style="margin:0;padding:40px 20px;background:#F8FAFC;font-family:Arial,sans-serif;">

  <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center">

        <table width="500" cellpadding="0" cellspacing="0"
          style="max-width:500px;background:#FFFFFF;border:1px solid #E2E8F0;border-radius:16px;padding:40px;">

          <tr>
            <td align="center" style="padding-bottom:24px;">
              <h1 style="margin:0;font-size:28px;color:#111827;">
                Our<span style="color:#7C3AED;">Memora</span>
              </h1>
            </td>
          </tr>

          <tr>
            <td align="center">
              <h2 style="margin:0 0 16px;color:#111827;font-size:22px;">
                Verifikasi Email
              </h2>

              <p style="margin:0 0 24px;color:#64748B;line-height:1.7;font-size:15px;">
                Halo <strong>{{ $user->name }}</strong>,
                terima kasih telah bergabung dengan OurMemora.
                Klik tombol di bawah untuk memverifikasi email Anda.
              </p>

              <a href="{{ $url }}"
                style="display:inline-block;background:#7C3AED;color:#FFFFFF;text-decoration:none;padding:14px 32px;border-radius:10px;font-weight:600;">
                Verifikasi Email
              </a>

              <p style="margin:28px 0 12px;color:#94A3B8;font-size:13px;">
                Atau buka tautan berikut:
              </p>

              <p style="word-break:break-all;font-size:12px;">
                <a href="{{ $url }}" style="color:#7C3AED;">
                  {{ $url }}
                </a>
              </p>

              <p style="margin-top:24px;color:#94A3B8;font-size:13px;">
                Link verifikasi berlaku selama 60 menit.
              </p>
            </td>
          </tr>

        </table>

        <p style="margin-top:20px;color:#94A3B8;font-size:12px;">
          © {{ date('Y') }} OurMemora
        </p>

      </td>
    </tr>
  </table>

</body>

</html>
```