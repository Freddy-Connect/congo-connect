Advanced Password Forget v2.1.0

Installation:
Installing the module is easy, just upload it using the FTP option in the Add & Manage section of the module administration.

* If your current version of Dolphin is v7.0.x than you need to make the following modification to your .htaccess file in the root of your Dolphin installation:

RewriteRule ^forgot.php   m/?r=forgot [QSA,L]


If You can't use that option for whatever reason:

- create the directory [dolphin root]/modules/denre
- Copy the zip file to the newly create folder and unzip it.
- Install the module via de admin panel

The installation contains the followingnew language strings:

	'_db_afo' => 'Advanced Forgot',
	'_db_pwd_not_same_err' => 'Passwords are not the same',
	'_db_pwd_length_err' => 'Password length is incorrect',

You can change these in the language settings.

The installation also contains a new email for password change confirmation and can be changed in the email templates section (Settings).
Uninstalling the package is just as easy. Just select the module and click uninstall.


What the module does:

1.It will store the new password + salt and an activation link  in newly added table.
2.It sends an email with the password (self set, or generated) and activation link to the user
3.The user clicks the (unique) activation link
4.The user id and activation link are checked and if correct:
	- The new password and salt are copied to the Password and Salt fields
	- The request is removed and can't be used again

6.The user is logged in and redirected to a page of (your) choice.

This means the new password is only set when the user clicks the activation link and the activation link can only be used once.


By default the "Email not found" message is disabled, a temporary password is generated and the redirect page is "profile edit".