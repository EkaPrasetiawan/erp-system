<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Water Kingdom Mekarsari</title>
    <link rel="stylesheet" href="css/styles.css" />
    <script
      src="https://use.fontawesome.com/releases/v6.3.0/js/all.js"
      crossorigin="anonymous"
    ></script>
  </head>
  <body style="background:
    linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
    url('assets/img/octopus.jpeg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    min-height: 100vh;
    ">
    <div id="layoutAuthentication">
      <div id="layoutAuthentication_content">
        <main>
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-5">
                <div class="card shadow-lg border-0 rounded-lg mt-5">
                  <div class="card-header" style="font-family: 'Franklin Gothic Medium';">
                    <img src="assets/img/logo1.png" alt="Logo" class="img-fluid logo-card">
                    <h4 class="text-center title-sub mb-0">Login System</h4>
                  </div>
                  <div class="card-body">
                    <form id="loginForm" method="POST">
                      <div class="form-floating mb-3">
                        <input
                          class="form-control"
                          id="userID"
                          name="userID"
                          type="email"
                          placeholder="name@example.com"
                          autocomplete="off" autofocus Required
                        />
                        <label for="userID">Email address</label>
                      </div>
                      <div class="form-floating mb-3">
                        <input
                          class="form-control"
                          id="password"
                          name="password"
                          type="password"
                          placeholder="Password"
                          autocomplete="off" Required
                        />
                        <label for="password">Password</label>
                      </div>
                      <div id="loginAlert" class="text-danger mb-2"></div>
                      <div>
                        <button type="submit" class="btn btn-primary btn-block" name="login">Login</button>
                      </div>
                    </form>
                  </div>
                  <div class="card-footer text-end">
                    <div class="small">
                      <p class="mt-1">Power By Trimitra Wahana Kresi</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
      crossorigin="anonymous"
    ></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/proses.js"></script>
  </body>
</html>
