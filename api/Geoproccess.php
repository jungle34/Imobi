<?PHP

include_once "Base.php";

class Geoproccess extends Base{
    function __construct(){
		$this->Connect();
	}

    function getBairros(){
        $query = "SELECT 
                        pgv_bairro 
                    FROM imobi.dados d 
                        WHERE pgv_bairro IS NOT NULL 
                        GROUP BY pgv_bairro 
                        ORDER BY pgv_bairro ASC";

        try{
            $check = $this->db->prepare($query);
            $check->execute();
        }catch(PDOException $Exception){
            $this->returnError("Ocorreu um erro durante a consulta de bairros");
        }

        $dados = array();
        while($item = $check->fetchObject()){
            $dados[] = $item->pgv_bairro;
        }

        $retorno = array("TYPE" => 'SUCCESS', "DADOS" => $dados);
        echo json_encode($retorno);
        die();
    }

    function loadData(){
        $bairro = !empty($_POST['chave']) ? $_POST['chave'] : false;
        if(!$bairro) $this->returnError("Requisição inválida");

        $query = "SELECT 
                        pgv_vvt,
                        pgv_a_cons,
                        (pgv_vvt / pgv_a_cons) AS m2_valor
                    FROM imobi.dados d
                        WHERE pgv_bairro = :chave";

        $variables = array(
            "chave" => $bairro
        );

        try{
            $check = $this->db->prepare($query);
            $check->execute($variables);
        }catch(PDOException $Exception){
            $this->returnError("Ocorreu um erro durante a consulta");
        }

        $imoveis = array();
        while($item = $check->fetchObject()){
            $imoveis[] = $item;
        }

        $dados = array(
            "qtd" => 0,
            "valor_venal_total" => 0,
            "area_total" => 0
        );

        $max = 0;
        $min = 0;
        
        foreach($imoveis as $key => $val){
            if($max < $val->pgv_vvt) $max = $val->pgv_vvt;
            
            if($val->pgv_vvt > 0 && ($min == null || $val->pgv_vvt < $min)) $min = $val->pgv_vvt;

            $dados['qtd']++;
            $dados['valor_venal_total'] += $val->pgv_vvt;
            $dados['area_total'] += $val->pgv_a_cons;
        }

        $dados['valor_venal_medio'] = $dados['valor_venal_total'] / $dados['qtd'];
        $dados['area_media'] = $dados['area_total'] / $dados['qtd'];        

        $media_media_vv = 0;        
        foreach($imoveis as $key => $val){
            $a1 = ($dados['valor_venal_medio'] - $val->pgv_vvt) ** 2;                     
            
            $media_media_vv += $a1 / $dados['qtd'];            
        }                   

        $dados['desvio_padrao_vv'] = sqrt($media_media_vv);        

        $dados['valor_max'] = $max;
        $dados['valor_min'] = $min;
        
        foreach($dados as $key => $value){
            if($key != 'qtd'){
                $dados[$key] = number_format($dados[$key], 2, ',', '.');
            }
        }        

        $retorno = array("TYPE" => 'SUCCESS', "DADOS" => $dados);
        echo json_encode($retorno);
        die();
    }
}