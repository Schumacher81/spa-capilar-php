CREATE DATABASE spa_capilar
    CHARACTER SET utf8mb4        
    COLLATE utf8mb4_unicode_ci; 
    
USE spa_capilar;

CREATE TABLE usuarios (
    id            INT          NOT NULL AUTO_INCREMENT,
    nome          VARCHAR(100) NOT NULL,
    login         VARCHAR(50)  NOT NULL,
    senha         VARCHAR(255) NOT NULL,  -- armazena hash bcrypt
    perfil        ENUM('ADMIN', 'PROFISSIONAL') NOT NULL DEFAULT 'PROFISSIONAL',
    ativo         TINYINT(1)   NOT NULL DEFAULT 1,  -- 1=ativo, 0=inativo
    criado_em     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uq_login (login)   -- login não pode se repetir
);

CREATE TABLE clientes (
    id               INT          NOT NULL AUTO_INCREMENT,
    nome             VARCHAR(100) NOT NULL,
    telefone         VARCHAR(20)  NOT NULL,
    email            VARCHAR(100),                    -- opcional
    data_nascimento  DATE,                            -- opcional
    observacoes      TEXT,                            -- texto longo
    ativo            TINYINT(1)   NOT NULL DEFAULT 1,
    criado_em        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uq_telefone (telefone)
);

CREATE TABLE agendamentos (
    id               INT          NOT NULL AUTO_INCREMENT,
    cliente_id       INT          NOT NULL,
    profissional_id  INT          NOT NULL,
    data_hora        DATETIME     NOT NULL,
    servico          VARCHAR(100) NOT NULL,
    status           ENUM('AGENDADO', 'REALIZADO', 'CANCELADO')
                                  NOT NULL DEFAULT 'AGENDADO',
    observacoes      TEXT,
    criado_em        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    -- Chaves estrangeiras — garantem integridade referencial
    -- Se deletar um cliente, não deixa deletar se tiver agendamentos
    CONSTRAINT fk_agendamento_cliente
        FOREIGN KEY (cliente_id)
        REFERENCES clientes(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_agendamento_profissional
        FOREIGN KEY (profissional_id)
        REFERENCES usuarios(id)
        ON DELETE RESTRICT
);

CREATE TABLE atendimentos (
    id                  INT          NOT NULL AUTO_INCREMENT,
    agendamento_id      INT          NOT NULL,
    produtos_utilizados TEXT,
    observacoes         TEXT,
    valor               DECIMAL(10,2),               -- nunca use FLOAT para dinheiro!
    realizado_em        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    -- Um agendamento só pode ter UM atendimento (UNIQUE)
    UNIQUE KEY uq_agendamento (agendamento_id),

    CONSTRAINT fk_atendimento_agendamento
        FOREIGN KEY (agendamento_id)
        REFERENCES agendamentos(id)
        ON DELETE RESTRICT
);

CREATE TABLE diagnosticos (
    id                      INT         NOT NULL AUTO_INCREMENT,
    atendimento_id          INT         NOT NULL,
    tipo_cabelo             VARCHAR(50),   -- Liso, Ondulado, Cacheado, Crespo
    porosidade              VARCHAR(30),   -- Baixa, Média, Alta
    oleosidade              VARCHAR(30),   -- Seco, Normal, Oleoso
    historico_quimico       TEXT,
    problemas               TEXT,
    tratamento_recomendado  TEXT,
    criado_em               DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    -- Um atendimento só pode ter UM diagnóstico (UNIQUE)
    UNIQUE KEY uq_atendimento (atendimento_id),

    CONSTRAINT fk_diagnostico_atendimento
        FOREIGN KEY (atendimento_id)
        REFERENCES atendimentos(id)
        ON DELETE RESTRICT
);

INSERT INTO usuarios (nome, login, senha, perfil) VALUES (
    'Administrador',
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'ADMIN'
);

CREATE VIEW vw_agendamentos AS
SELECT
    a.id,
    a.data_hora,
    a.servico,
    a.status,
    a.observacoes,
    a.criado_em,
    c.id           AS cliente_id,
    c.nome         AS cliente_nome,
    c.telefone     AS cliente_telefone,
    u.id           AS profissional_id,
    u.nome         AS profissional_nome
FROM agendamentos a
INNER JOIN clientes  c ON c.id = a.cliente_id
INNER JOIN usuarios  u ON u.id = a.profissional_id
ORDER BY a.data_hora ASC;

CREATE VIEW vw_atendimentos AS
SELECT
    at.id,
    at.produtos_utilizados,
    at.observacoes,
    at.valor,
    at.realizado_em,
    ag.id          AS agendamento_id,
    ag.servico,
    ag.data_hora,
    c.id           AS cliente_id,
    c.nome         AS cliente_nome,
    u.nome         AS profissional_nome
FROM atendimentos at
INNER JOIN agendamentos ag ON ag.id = at.agendamento_id
INNER JOIN clientes     c  ON c.id  = ag.cliente_id
INNER JOIN usuarios     u  ON u.id  = ag.profissional_id
ORDER BY at.realizado_em DESC;

SELECT 'TABELAS CRIADAS:' AS info;
SHOW TABLES;
