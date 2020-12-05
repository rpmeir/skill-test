
CREATE TABLE etapa_atendimento (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    nome varchar(100),
    cor varchar(100),
    ordem int);

CREATE TABLE cliente (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    nome varchar(100),
    data_nasc date,
    foto varchar(300));

CREATE TABLE prioridade (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    nome varchar(100),
    cor varchar(100));

CREATE TABLE atendimento (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    descricao varchar(100),
    data_atend date,
    ordem int,
    etapa_atendimento_id int NOT NULL,
    cliente_id int NOT NULL,
    prioridade_id int NOT NULL,
    FOREIGN KEY(etapa_atendimento_id) REFERENCES etapa_atendimento(id),
    FOREIGN KEY(cliente_id) REFERENCES cliente(id),
    FOREIGN KEY(prioridade_id) REFERENCES prioridade(id));

CREATE TABLE canal (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    nome varchar(100),
    cor varchar(100));

CREATE TABLE canal_atendimento (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    atendimento_id int NOT NULL,
    canal_id int NOT NULL,
    FOREIGN KEY(atendimento_id) REFERENCES atendimento(id),
    FOREIGN KEY(canal_id) REFERENCES canal(id));

INSERT into etapa_atendimento (nome, cor, ordem) values
('Recebidos', 'red', 1),
('Em análise', 'orange', 2),
('Finalizados', 'green', 3);

INSERT  into prioridade (nome, cor) values
('Baixa', 'green'),
('Média', 'orange'),
('Alta', 'red');

INSERT  into canal (nome, cor) values
('Web', 'pink'),
('Mobile', 'purple'),
('Email', 'cyan');
