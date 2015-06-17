      <?php
      
      $data = [];
      
      while(list($index, $value) = each($_SERVER))
      {
      	switch  ($index) {
                case "Shib-eduPerson-eduPersonAffiliation":
                  $data['affiliation'] = $value;
                  break;
                case "Shib-inetOrgPerson-cn":
                  $data['name'] = $value;
                  break;
                case "Shib-inetOrgPerson-mail":
                  $data['email'] = $value;
                  break;
                case "Shib-brEduPerson-brEduAffiliationType":
                  $data['affType'] = $value;
                  break;
        }
      }

      $data = json_encode($data);

      session_start();
      $_SESSION['data_from_cafe'] = $data;
      header('Location: '."https://meican.cipo.rnp.br/init/login/cafe", true, 302);
?>

