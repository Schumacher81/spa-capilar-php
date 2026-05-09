<?php

/**
 * UsuarioModel — Model de usuários
 * 
 * Responsável por todas as operações de banco
 * relacionadas à tabela "usuarios"
 * 
 * Herda de Model (que já tem a conexão PDO)
 */
class UsuarioModel extends Model {

    // Define a tabela que este model gerencia
    protected string $tabela = 'usuarios';

    // ================================================
    // BUSCAR USUÁRIO PELO LOGIN
    // Usado no processo de autenticação
    // ================================================
    public function buscarPorLogin(string $login): array|false {
        $stmt = $this->db->prepare(
            "SELECT * FROM usuarios 
             WHERE login = :login 
             AND ativo = 1 
             LIMIT 1"
        );
        $stmt->execute([':login' => $login]);
        return $stmt->fetch();
    }

    // ================================================
    // VERIFICAR SENHA
    // Compara a senha digitada com o hash do banco
    // password_verify() é a função nativa do PHP
    // para verificar senhas criptografadas com BCrypt
    // ================================================
    public function verificarSenha(string $senha,
                                   string $hash): bool {
        return password_verify($senha, $hash);
    }

    // ================================================
    // AUTENTICAR USUÁRIO
    // Junta busca + verificação em um único método
    // Retorna o usuário se autenticado, false se não
    // ================================================
    public function autenticar(string $login,
                               string $senha): array|false {

        // Busca o usuário pelo login
        $usuario = $this->buscarPorLogin($login);

        // Se não encontrou ou senha errada → retorna false
        if (!$usuario) {
            return false;
        }

        if (!$this->verificarSenha($senha, $usuario['senha'])) {
            return false;
        }

        // Remove a senha do array antes de retornar
        // Nunca deixe a senha trafegar desnecessariamente!
        unset($usuario['senha']);

        return $usuario;
    }

    // ================================================
    // LISTAR TODOS OS USUÁRIOS ATIVOS
    // ================================================
    public function listarAtivos(): array {
        $stmt = $this->db->prepare(
            "SELECT id, nome, login, perfil, criado_em 
             FROM usuarios 
             WHERE ativo = 1 
             ORDER BY nome ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ================================================
    // LISTAR PROFISSIONAIS (para dropdown de agendamento)
    // ================================================
    public function listarProfissionais(): array {
        $stmt = $this->db->prepare(
            "SELECT id, nome 
             FROM usuarios 
             WHERE perfil = 'PROFISSIONAL' 
             AND ativo = 1 
             ORDER BY nome ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ================================================
    // VERIFICAR SE LOGIN JÁ EXISTE
    // ================================================
    public function loginExiste(string $login,
                                int $excluirId = 0): bool {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM usuarios 
             WHERE login = :login 
             AND id != :id"
        );
        $stmt->execute([
            ':login' => $login,
            ':id'    => $excluirId
        ]);
        return (int) $stmt->fetchColumn() > 0;
    }

    // ================================================
    // SALVAR NOVO USUÁRIO
    // ================================================
    public function salvar(array $dados): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO usuarios (nome, login, senha, perfil) 
             VALUES (:nome, :login, :senha, :perfil)"
        );

        return $stmt->execute([
            ':nome'   => $dados['nome'],
            ':login'  => $dados['login'],
            // Criptografa a senha com BCrypt antes de salvar
            ':senha'  => password_hash($dados['senha'], PASSWORD_BCRYPT),
            ':perfil' => $dados['perfil']
        ]);
    }

    // ================================================
    // ATUALIZAR USUÁRIO
    // ================================================
    public function atualizar(int $id, array $dados): bool {

        // Se veio nova senha, atualiza também
        if (!empty($dados['senha'])) {
            $stmt = $this->db->prepare(
                "UPDATE usuarios 
                 SET nome = :nome, login = :login, 
                     senha = :senha, perfil = :perfil 
                 WHERE id = :id"
            );
            return $stmt->execute([
                ':nome'   => $dados['nome'],
                ':login'  => $dados['login'],
                ':senha'  => password_hash(
                                $dados['senha'], PASSWORD_BCRYPT
                             ),
                ':perfil' => $dados['perfil'],
                ':id'     => $id
            ]);
        }

        // Sem nova senha — mantém a atual
        $stmt = $this->db->prepare(
            "UPDATE usuarios 
             SET nome = :nome, login = :login, perfil = :perfil 
             WHERE id = :id"
        );
        return $stmt->execute([
            ':nome'   => $dados['nome'],
            ':login'  => $dados['login'],
            ':perfil' => $dados['perfil'],
            ':id'     => $id
        ]);
    }

    // ================================================
    // INATIVAR USUÁRIO (nunca deletar!)
    // ================================================
    public function inativar(int $id): bool {
        $stmt = $this->db->prepare(
            "UPDATE usuarios SET ativo = 0 WHERE id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }
}