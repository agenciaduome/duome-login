# Custom Login (by duo.me)

This is a duo.me WordPress plugin for frontend custom login. Call the fields below in your theme.

## Login form
- Form with `method="post"` and `action=""`.
- Call `<?php wp_nonce_field( 'duome_login' ); ?>` inside it.
- Username input with `name="duome_login_username"`.
- Password input with `name="duome_login_password"`.
- Show messages with `<?php do_action( 'duome_login_errors', 'login' ); ?>`
- Optional remember checkbox with `name="duome_login_remember"` and `value="1"`.
