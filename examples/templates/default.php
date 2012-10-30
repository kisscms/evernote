<html>
  <head>
    <title>Evernote KISSCMS Login Demo</title>
  </head>
  <body>

    <h1>Evernote KISSCMS Login Demo</h1>

    <p>
      This application demonstrates the use of OAuth to authenticate to the Evernote web service.
      OAuth support is implemented using the <a href="http://github.com/kisscms/oauth">KISSCMS OAuth plugin</a>.
    </p>

    <hr>
    
    <h2>Evernote Authentication</h2>
    
<? if( !Evernote::login() ){ ?>

    <p>
      On this page, we demonstrate a one step process for OAuth authentication.
    </p>
    
    <p>
      <a href="<? Evernote_OAuth::link() ?>">Click here</a> to authorize this application to access your Evernote account. You will be directed to evernote.com to authorize access, then returned to this application after authorization is complete.
    </p>

    
<? } else { ?>
    <p style="color:green">
      Congratulations, you have successfully authorized this application to access your Evernote account!
    </p>

    <p>
      If you would like more detail on your authentication details (not typically expose a user), 
      <a href="/credentials">click here</a> to view a page listing the session variables.
    </p>
    
    <p>
      Your account contains the following notebooks:
    </p>
	
    <? Template::body(); ?>
    
    <p>
      <a href="<?=url("/logout") ?>">Click here</a> to start over.
    </p>
    
<? } ?>

  </body>
</html>