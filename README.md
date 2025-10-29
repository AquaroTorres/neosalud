# Servicio de Salud Tarapacá

# NeoSalud - Portal Interno de sistemas

## Dependencias Windows con WSL2

-   [Instalar WSL2](https://docs.microsoft.com/es-es/windows/wsl/install)
-   [Instalar Docker Desktop](https://docs.docker.com/desktop/windows/install)
-   Instalar Git en WSL2 (ej: `$ sudo apt-get install git`)
-   MySql (en el host o en un contenedor)

## Dependencias Mac

-   [Instalar Docker Desktop](https://www.docker.com/get-started/)
-   Instalar Git
-   MySql (en el host o en un contenedor)

## Instalación

-   Abrir un terminal de WSL (opcional [Instalar Windows Terminal](https://docs.microsoft.com/es-es/windows/terminal/))
-   `git clone https://github.com/cl-ssi/neosalud`
-   `cd neosalud`
-   `cp .env.example .env`
-   Configurar los datos de conexión a tu base de datos "unisalud" de tu local en el .env
-   `docker build -t neosalud docker/dev`
-   `docker run --rm -it -v $(pwd):/var/www/html -p 8000:8000 -d --name neosalud neosalud`
-   `docker exec -it neosalud /bin/bash`
-   Esto abrirá un contenedor con nuestra aplicación
-   `composer install`
-   `php artisan key:generate`
-   `php artisan serve --host=0.0.0.0 --port=8000`
-   Pudese usar el alias `$ serve` para este último comando, ver todos los alias: `$ alias`

## Para detener el contenedor

-   `docker stop neosalud`

## Abrir el navegador

-   Ir a http://localhost:8000

## Alias para los comandos en bash

-   `` alias dbuild='docker build -t `basename "$PWD"` docker/dev' ``
-   `` alias drun='docker run --rm -it -v $(pwd):/var/www/html -p 8000:8000 -d --name `basename "$PWD"` `basename "$PWD"`' ``
-   `` alias dexec='docker exec -it `basename "$PWD"` /bin/bash' ``

## License

Open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
