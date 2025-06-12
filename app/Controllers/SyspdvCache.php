<?php

class SyspdvCache {
    private $dbConfig;
    private $cacheDir;
    private $produtosFile;
    private $funcionariosFile;
    private $cacheTime = 3600; // 1 hora em segundos

    // Campos que serão buscados (personalizáveis)
    private $camposProduto = [
        'PROCOD' => 'id',
        'PRODES' => 'nome',
        'PROPRCVDAVAR' => 'preco'
    ];
    
    private $camposFuncionario = [
        'FUNCOD' => 'id',
        'FUNDES' => 'nome'
    ];

    public function __construct($dbConfig) {
        $this->dbConfig = $dbConfig;
        $this->cacheDir = __DIR__ . '/cache/';
        $this->produtosFile = $this->cacheDir . 'produtos.json';
        $this->funcionariosFile = $this->cacheDir . 'funcionarios.json';
        
        if (!file_exists($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    private function connect() {
        $dsn = "firebird:dbname={$this->dbConfig['host']}:{$this->dbConfig['path']};charset={$this->dbConfig['charset']}";
        $username = $this->dbConfig['username'];
        $password = $this->dbConfig['password'];
        
        try {
            $conn = new PDO($dsn, $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            throw new Exception("Erro na conexão com o banco: " . $e->getMessage());
        }
    }

    // Processa os resultados para estrutura limpa
    private function processarResultados($dados, $mapaCampos) {
        $resultado = [];
        
        foreach ($dados as $item) {
            $novoItem = [];
            foreach ($mapaCampos as $campoOriginal => $campoNovo) {
                if (isset($item[$campoOriginal])) {
                    // Remove espaços em branco do final para campos string
                    $valor = $item[$campoOriginal];
                    if (is_string($valor)) {
                        $valor = rtrim($valor);
                    }
                    
                    // Converte para float se for um campo de preço
                    if ($campoNovo === 'preco') {
                        $valor = (float)$valor;
                    }
                    
                    // Converte para int se for um campo de ID
                    if ($campoNovo === 'id') {
                        $valor = (int)$valor;
                    }
                    
                    $novoItem[$campoNovo] = $valor;
                }
            }
            $resultado[] = $novoItem;
        }
        
        return $resultado;
    }

    public function atualizarProdutosCache() {
        try {
            $conn = $this->connect();
            
            // Monta a query com apenas os campos necessários
            $campos = implode(', ', array_keys($this->camposProduto));
            $query = "SELECT $campos FROM PRODUTO"; // Exemplo com filtro
            
            $stmt = $conn->query($query);
            $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Processa para estrutura limpa
            $produtosProcessados = $this->processarResultados($produtos, $this->camposProduto);
            
            // Gera JSON formatado
            file_put_contents(
                $this->produtosFile, 
                json_encode($produtosProcessados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );
            return true;
        } catch (Exception $e) {
            error_log("Erro ao atualizar produtos: " . $e->getMessage());
            return false;
        }
    }

    public function atualizarFuncionariosCache() {
        try {
            $conn = $this->connect();
            
            $campos = implode(', ', array_keys($this->camposFuncionario));
            $query = "SELECT $campos FROM FUNCIONARIO";
            
            $stmt = $conn->query($query);
            $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $funcionariosProcessados = $this->processarResultados($funcionarios, $this->camposFuncionario);
            
            file_put_contents(
                $this->funcionariosFile, 
                json_encode($funcionariosProcessados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );
            return true;
        } catch (Exception $e) {
            error_log("Erro ao atualizar funcionários: " . $e->getMessage());
            return false;
        }
    }

    // ... (mantenha os outros métodos como getProdutos, getFuncionarios, etc)
    
    /**
     * Define quais campos de produto devem ser buscados
     * @param array $campos Array no formato ['CAMPO_TABELA' => 'nome_no_json']
     */
    public function setCamposProduto(array $campos) {
        $this->camposProduto = $campos;
    }
    
    /**
     * Define quais campos de funcionário devem ser buscados
     * @param array $campos Array no formato ['CAMPO_TABELA' => 'nome_no_json']
     */
    public function setCamposFuncionario(array $campos) {
        $this->camposFuncionario = $campos;
    }
}