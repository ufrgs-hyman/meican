## MEICAN Build instructions :wrench:

### 1- Choose the settings for the image that will be generated: :mag:

  * The version of MEICAN you want to build.

  * A name for the image that will be created.

<p>&nbsp&nbsp :warning: In the following commands, <b><em>3.3.0</em></b> will be used as the chosen version and <b><em>ghcr.io/ufrgs-hyman/meican/meican-app:3.3.0</em></b> will be used as the chosen image name. 
When you go to create an image, don't forget to change these values to the ones of your choice.<p>

<br>

### 2- Build the MEICAN image: :gear:
  * Go to 'docker_for_build' directory.
  * Run: ```docker build --build-arg MEICAN_VERSION=3.3.0 -t ghcr.io/ufrgs-hyman/meican/meican-app:3.3.0 .```

<div>&nbsp&nbsp :warning: Note that there is a dot at the end of the command.</div>
<div>&nbsp&nbsp :triangular_flag_on_post:After running, the image will be created on your local machine.</div>

<br>

### 3- Authenticate to the Container Registry: :key:

  * To authenticate, follow the instructions in the following link: https://docs.github.com/pt/packages/working-with-a-github-packages-registry/working-with-the-container-registry#

<br>

### 4- Submit the image to GitHub Container Registry: :arrow_heading_up:

  * Run: ```docker push ghcr.io/ufrgs-hyman/meican/meican-app:3.3.0```
   
<div>&nbsp&nbsp :warning: Note that <b><em>ghcr.io/ufrgs-hyman/meican/meican-app:3.3.0</em></b> is the name you set for the image.</div>

<br>
<br>

:heavy_check_mark: After these steps your new image will be published on GitHub Container Registry! :confetti_ball: :tada:
