# skill-test
Skill Test


Acessar a pasta raiz do projeto e rodar o seguinte comando para criar as imagens do zero:

make docker-build-from-scratch

Ou ainda, após alguma modificação:

make docker-build

Serão criados quatro imagens/containers:

- php-cli
- php-fpm
- nginx
- mysql

Após criadas as imagens, podemos apenas rodar o camando a seguir para subir os containers:

make docker-up

E para encerrar os containers:

make docker-down

O arquivo .env contém as variáveis de ambiente para configurar o sistema.

Porta do servidor http: 8090

O docker-compose.yml contém as configurações de rede internas dos containers e mapeamentos entre os arquivos de sistema.
