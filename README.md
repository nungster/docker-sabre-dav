docker-sabre-dav
================

Using Docker compose, just execute:
```
docker-compose up -d
```
Docker Compose will build the SaberDAV container and a Litmus cardDAV test container.  Litmus will run its suite of tests against the SaberDAV container.
You can view the output of the Litmus tests.

Execute a:
```
docker ps -a
```
To get a list of the containers, then to view the logs:
```
docker logs <Name of your Litmus container>
```
The Litmus container can be started and you may modify or rerun the tests.
```
docker run -ti <ID of your Litmus container> bash -l
```
The default credentials for the sabreDAV container were set in the docker-compose.yml file as the environment variable, NGINX_AUTH_BASIC
