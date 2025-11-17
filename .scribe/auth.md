# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer {YOUR_AUTH_TOKEN}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

You can retrieve your token by calling the <code>/api/login</code> or <code>/api/register</code> endpoints. Use the token in the <code>Authorization</code> header as <code>Bearer {token}</code>.
