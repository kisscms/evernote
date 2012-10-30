<html>
  <head>
    <title>Evernote Credentials</title>
  </head>
  <body>

    <h1>Evernote Credentials</h1>

    <p>
      <a href="<?=url("/logout") ?>">Click here</a> to logout
    </p>

    <hr/>
    
    <h2>Current status</h2>
    <p>
      <b>Evernote server:</b> <?php echo EVERNOTE_SERVER; ?>
      <br/>
      <b>NoteStore Host:</b> <?php echo NOTESTORE_HOST; ?>
      <br/>
      <b>NoteStore Protocol:</b> <?php echo NOTESTORE_PROTOCOL; ?>
	</p>

    <p>
      <b>NoteStore URL:</b> <?php echo $_SESSION['oauth']['evernote']['edam_noteStoreUrl']; ?>
      <br/>
      <b>Web API URL prefix:</b> <?php echo $_SESSION['oauth']['evernote']['edam_webApiUrlPrefix']; ?>
	</p>
    
    <b>Token credentials:</b>
    <ul>
      <li><b>Identifier:</b><br/><?php echo $_SESSION['oauth']['evernote']['oauth_token']; ?>
      <li><b>Secret:</b><br/><?php echo $_SESSION['oauth']['evernote']['oauth_token_secret']; ?>
      <li><b>User ID:</b><br/><?php echo $_SESSION['oauth']['evernote']['edam_userId']; ?>
      <li><b>Expires:</b><br/><?php echo date(DATE_RFC1123, $_SESSION['oauth']['evernote']['edam_expires']); ?>
    </ul>
  
  </body>
</html>