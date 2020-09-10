## Webmim Docker

### Requirements:
* <a href="https://docs.docker.com/install">Docker</a>


### Instructions to run:


1- Enter the "docker-webmin" directory and run the following command to create an image:
      
      docker build -t imagem_webmim .
        


2- You now have an image. Just create a container from that image. To do this, run the command:
  
      docker run --cap-add=NET_ADMIN --cap-add=NET_RAW -d -p 10000:10000 --name container_webmin imagem_webmim



3- After, Webmin will be available at localhost in port 10000. Access localhost: 10000 in your browser (does not work on Google Chrome). Your credentials are:

```
user: root
pass: password
```

