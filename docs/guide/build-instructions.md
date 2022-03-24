## MEICAN Build instructions


1- Choose the settings for the image that will be generated:

  * The version of MEICAN you want to build.

  * A name for the image that will be created.

 In the following command, '_3.3.0-rc_' is the chosen version and '_meican-app:3.3.0-rc_' is the chosen image name.


2- Go to 'docker_for_build' directory and run.

```docker build --build-arg MEICAN_VERSION=3.3.0-rc meican-app:3.3.0-rc .```

After execution, the image will be ready to use.
