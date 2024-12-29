
<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="img/logo-magelo.PNG" type="">
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
      integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <link rel="stylesheet" href="css/style.css" />
    <title>MAGELO</title>
  </head>
  <body>
    <div class="login-container">
      <div class="login-box">
        <div class="login-logo">
          <img src="img/logo-magelo.PNG" alt="logotipo" />
        </div>
        <form id="loginForm" action="processar_login.php" method="POST">
          <div class="input-group">
            <i class="fa-solid fa-user"></i>
            <input type="text" id="usuario" name="usuario" placeholder="usuário" required />
            <span class="error-message" id="usuarioError"></span>
          </div>
        
          <div class="input-group">
            <i class="fa-solid fa-lock"></i>
            <input type="password" id="senha" name="senha" placeholder="palavra-passe" required />
            <span class="error-message" id="senhaError"></span>
          </div>
        
          <button type="submit" class="login-button">ENTRAR</button>
        </form>
      </div>
    </div>

    
  </body>
</html>
