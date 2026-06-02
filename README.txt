#Para generar construir y levantar el CONTENEDOR
docker-compose up --build

#Para depuración de errores
docker-compose logs -f backend
docker-compose logs -f frontend

#Detiene y borra los contenedores que esten corriendo
docker-compose down
docker-compose down -v #Borra los volumenes
