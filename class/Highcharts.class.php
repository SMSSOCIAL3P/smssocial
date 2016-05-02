<?php
/**
 * Classe responsável pelas criação do grafico 
 * e contem funções simplificadas
 * para facilitar o uso.
 * 
 * @author 
 * @version 1.0
 * @package Highcharts
 */
class Highcharts extends HighchartsOptions {
	
	/**
	 * Categorias do grafico, valores do eixo X
	 */
	private $categories = array();
	
	/**
	 * Valores a serem printados no grafico, representa as linhas, colunas, etc...
	 */
	private $series = array();
	
	/**
	 * Array para informar qual valor no grafico será mostrado ou não no momento de renderizar.
	 */
	private $visible = array();
	
	/**
	 * Array das combinações de gráficos
	 */
	private $combination = array();
	
	/**
	 * Seta o tipo de combinação.
	 */
	private $combinationType = '';
	
	/**
	 * Informa o valor onde será setado a coluna/linha como Hide. 
	 */
	private $valToHide = 0;
	
	public function __construct($grafTipo = '',$tagId = ''){
	
		parent::__construct();

		if($grafTipo != '')
			$this->setTipoGrafico($grafTipo);
			
		if($tagId != '')
			$this->setTagId($tagId);
			
	}
	
	/**
	 * <pre>&nbsp;
	 * Define o tipo de grafico: coluna, linha, pizza...
	 * Parametros:
	 * 	GRAF_COLUNA
	 * 	GRAF_BARRA
	 * 	GRAF_LINHA
	 * 	GRAF_PIZZA
	 * </pre>
	 * @param string $tipo tipo de gráfico desejado
	 * @example exemplos/setExemplo.php Arquivo de Exemplo
	 */
	public function setTipoGrafico($tipo){
		
		$this->setChart(array('defaultSeriesType' => $tipo));
		
	}
	
	/**
	 * <pre>
	 * &nbsp;
	 * Seta o id do elemento onde o graficos sera renderizado
	 * </pre>
	 * @param string $id Id do elemento
	 * @example exemplos/setExemplo.php Arquivo de Exemplo
	 */
	public function setTagId($id){
		$this->setChart(array('renderTo' => $id));
	}

	/**
	 * <pre>&nbsp;
	 * Seta o titulo do grafico
	 * </pre>
	 * @param string $titulo
	 * @example exemplos/setExemplo.php Arquivo de Exemplo
	 */
	public function setTitulo($titulo){
		$this->setTitle(array('text' => utf8_encode($titulo)));
	}

	/**
	 * <pre>&nbsp;
	 * Seta o subtitulo do grafico
	 * </pre>
	 * @param string $subTitulo
	 * @example exemplos/setExemplo.php Arquivo de Exemplo
	 */
	public function setSubTitulo($subTitulo){
		$this->setSubtitle(array('text' => utf8_encode($subTitulo)));
	}

	/**
	 * <pre>&nbsp;
	 * Seta o tipo de combinacao
	 * 
	 * COMB_SIMPLES 
	 * 	Monta o grafico com os valores passados
	 * COMB_RELACIONADA 
	 * 	Para o grafico PIZZA faz a soma dos valores. 
	 * 	Para o grafico LINHA faz a media. 
	 * 	Para o de COLUNA monta com os valores passados.
	 * </pre>
	 * @param string $type
	 * @example exemplos/setExemplo.php Arquivo de Exemplo
	 */
	public function setCombinationType($type){
		
		$this->combinationType = $type;
		
	}
	
	/**
	 * <pre>&nbsp;
	 * Seta o titulo lateral(eixo Y) do grafico
	 * </pre>
	 * @param string $titulo
	 * @example exemplos/setExemplo.php Arquivo de Exemplo
	 */
	public function setTituloLateral($titulo){
		$this->setYAxis(array('title' =>array('text' => utf8_encode($titulo))));
	}
	
	/**
	 * <pre>&nbsp;
	 * Quando setado este valor, no momento de criação do grafico, caso o maior valor seja igual 
	 * ao menor + o valor informado, o mesmo é escondido para melhor visualização. 
	 * </pre>
	 * @param int $valToHide
	 * @example exemplos/setExemplo.php Arquivo de Exemplo
	 */
	public function setValToHide($valToHide){
		$this->valToHide = $valToHide;
	}
	
	/**
	 * <pre>&nbsp;
	 * Seta os graficos a serem combinados, pode ser passado um tipo de grafico ou um array
	 * 		
	 * Ex.: $hc->addGrafico(Highcharts::GRAF_PIZZA);
	 *      $hc->addCategoria(array(Highcharts::GRAF_PIZZA,Highcharts::GRAF_COLUNA));
	 * 
	 * Obs.: nao eh preciso adicionar o grafico padrao, faz a combinacao automaticamente.
	 * </pre>
	 * @param multitype $grafico
	 * @example exemplos/addGrafico.php Arquivo de Exemplo
	 */
	public function addGrafico($grafico){
		
		if(is_array($grafico))
			$this->combination = array_merge($this->combination,$grafico);
		else
			array_push($this->combination, $grafico);

			
		$this->setFunctions(Highcharts::FUNCAO_TOOLTIP,array('formatter'=>array('funcao' => "var s;if (this.point.name) { s = '<b>'+this.point.name +'</b>: '+ this.y;} else {s = '<b>'+this.series.name  +'</b>: '+ this.y;}return s;",'tipo' => Highcharts::FUNC_CRIAR)));
	}
	
	/**
	 * <pre>&nbsp;
	 * Adiciona uma categoria ao grafico, pode ser passado uma categoria ou um array de categorias
	 * 
	 * Ex.: $hc->addCategoria('Brasil');
	 *      $hc->addCategoria(array('Brasil','EUA'));
	 * </pre>
	 * @param multitype $categoria
	 * @example exemplos/addCategoriaValores.php Arquivo de Exemplo
	 */
	public function addCategoria($categoria){
		if(is_array($categoria))
			$this->categories = array_merge($this->categories,$categoria);
		else
			array_push($this->categories, $categoria);
	}
	
	/**
	 * <pre>&nbsp;
	 * Adiciona os valores, deve ser passado o nome referente aos valores(nome que aparecera na legenda),
	 *  os vallores podem ser string ou array
	 * </pre>
	 * @param string $nome Nome referente aos valores
	 * @param multitype $valores Valores
	 * @example exemplos/addCategoriaValores.php Arquivo de Exemplo
	 */
	public function addValores($nome,$valores){

		if(!isset($this->series[$nome])){
			if(is_array($valores))
				$this->series[$nome] = $this->parseArrFloat($valores);
			else{
				$this->series[$nome] = array();
				array_push($this->series[$nome], floatval($valores));
			}
		}else{
			
			if(is_array($valores))
				$this->series[$nome] = array_merge($this->series[$nome],$this->parseArrFloat($valores));
			else 
				array_push($this->series[$nome], floatval($valores));
			
		}
	}
	
	/**
	 * <pre>&nbsp;
	 * Agrupa os valores de cada categoria em uma unica coluna
	 * </pre>
	 * @param boolean $bool
	 * @example exemplos/booleanExemplos.php Arquivo de Exemplo
	 */
	public function agruparColuna($bool){
		
		if($bool)
			$this->setPlotOptions(array('column' => array('stacking' => 'normal')));
		else
			$this->setPlotOptions(array('column' => array('stacking' => null)));
		
	}
	
	/**
	 * <pre>&nbsp;
	 * Informa qual valor no grafico sera iniciado como hidden(escondido), pois quando 
	 * o grafico possui um valor muito superior aos outros, fica difícil a interpretação 
	 * do mesmo, assim quando o maior valor é escondido a visualização fica mais clara.
	 * </pre>
	 * @param multitype $value
	 * @example exemplos/hideValue.php Arquivo de Exemplo
	 */
	public function hideValue($value){
		
		if(is_array($value)){
			foreach ($value as $name)
				$this->visible[$name] = false;
		}else{
			$this->visible[$value] = false;
		}
		
	}
	
	/**
	 * <pre>&nbsp;
	 * Para o grafico PIZZA, o valor setado pela funcao {@link showValue()} ficará em 
	 * evidencia no momendo de criação do grafico.
	 * Para os demais graficos, caso algum valor seja setado anteriormente para nao se 
	 * exibido({@link hideValue()}) pode ser mostrado utilizando esta funcao.
	 * </pre>
	 * @param multitype $value
	 * @example exemplos/showValue.php Arquivo de Exemplo
	 */
	public function showValue($value){
		
		if(is_array($value)){
			foreach ($value as $name)
				$this->visible[$name] = true;
		}else{
			$this->visible[$value] = true;
		}
		
	}
	
	/**
	 * <pre>&nbsp;
	 * Informa se a legenda sera mostrada.
	 * </pre>
	 * @param $bool
	 * @example exemplos/booleanExemplos.php Arquivo de Exemplo
	 */
	public function showLegend($bool){
		$this->setLegend(array('enabled'=>$bool));
	}
	
	/**
	 * <pre>&nbsp;
	 * Seta a variavel para mostrar o label do grafico Pizza ou nao
	 * </pre>
	 * @param $bool
	 * @example exemplos/booleanExemplos.php Arquivo de Exemplo
	 */
	public function showPieLabel($bool){
		$this->setPlotOptions(array('pie'=>array('dataLabels'=>array('enabled' => $bool))));
	}
	
	/**
	 * <pre>&nbsp;
	 * Inverte o grafico
	 * </pre>
	 * @param $bool
	 * @example exemplos/booleanExemplos.php Arquivo de Exemplo
	 */
	public function inverter($bool){
		$this->setChart(array('inverted'=>$bool));
	}	

	/**
	 * <pre>&nbsp;
	 * Retorna o tipo de grafico, COLUNA, PIZZA, BARRA, etc...
	 * </pre>
	 * @return string
	 */
	public function getTipoGrafico(){
		return $this->getChart('defaultSeriesType');
	}
	
	/**
	 * <pre>&nbsp;
	 * Retorna um array com as categorias setadas anteriormente em {@link setCategoria()}.
	 * </pre>
	 * @return array
	 */
	public function getCategoria(){
		return $this->categories;
	}
	
	/**
	 * <pre>&nbsp;
	 * Retorna um array com os valores setadas anteriormente em {@link setValores()}.
	 * </pre>
	 * @return array
	 */
	public function getValores(){
		return $this->series;
	}
	
	/**
	 * <pre>&nbsp;
	 * Retorna um array com os tipos de graficos setados para efetuar a combinação.
	 * </pre>
	 * @return array
	 */
	public function getCombination(){
		return $this->combination;
	}
	
	/**
	 * <pre>&nbsp;
	 * Retorna o valor para fazer a verificação se deve esconder a linha/coluna.
	 * </pre>
	 * @return int
	 */
	public function getValToHide(){
		return $this->valToHide;
	}
	
	/**
	 * <pre>&nbsp;
	 * Monta o array de valores para o grafico COLUNA, LINHA e AREA, caso a variavel type seja passado significa 
	 * que eh um grafico combinado, assim a funcao monta o array de uma maneira diferente.
	 * <pre>
	 * @param array $valores
	 * @param string $type
	 * @return array Array com os valores para criacao do json
	 * @example exemplos/getSeries.php Arquivo de Exemplo
	 */
	public function getColumLineSerie($valores,$type = ''){
		
		$json = array();
		$colors = $this->getColors();
		$i = 0;
		foreach($valores as $name => $data){
			
			$visible = true;
			if(!isset($colors[$i]))
				$i = 0;
			if(isset($this->visible[$name]))
				$visible = $this->visible[$name];
				
				$val = array('name' => $name,'visible' => $visible,'data' => $data,'color' => $colors[$i]);
				if($type != '')
					$val['type'] = $type;
					
				array_push($json, $val);
				
			$i++;
		}
		
		return $json;
		
	}
	
	/**
	 * <pre>&nbsp;
	 * Monta o array de valores para o grafico PIZZA, caso a variavel type seja passado significa 
	 * que eh um grafico combinado, assim a funcao monta o array de uma maneira diferente.
	 * <pre>
	 * @param array $valores
	 * @param string $type
	 * @return array Array com os valores para criacao do json
	 * @example exemplos/getSeries.php Arquivo de Exemplo
	 */
	public function getPieSerie($valores,$type = ''){

		$json = array();
		$colors = $this->getColors();
		$i = 0;
		foreach($valores as $name => $data){

			$visible = false;
			if(!isset($colors[$i]))
				$i = 0;
				
			if(isset($this->visible[$name]))
				$visible = $this->visible[$name];
				
			if($visible){
				$val = array('name' => $name,'y' => $this->getValorSerie($data,'soma'),'sliced' => true,'selected' => true,'color' => $colors[$i]);
				array_push($json,$val);
			}else{
				$val = array('name' => $name,'y' => $this->getValorSerie($data,'soma'),'color' => $colors[$i]);
				array_push($json, $val);
			}

			$i++;
		}

		$graficos = $this->getCombination();
		array_push($graficos, $this->getTipoGrafico());
		
		if(array_search(self::GRAF_BARRA, $graficos) !== FALSE)
			$position = array('90%',80);
		else
			$position = array(100,80);
		
		
		if($type == '')
			return array(array('type' => self::GRAF_PIZZA,'name' => 'Grafico Pizza','data' => $json));
		else
			return array(array('type' => self::GRAF_PIZZA,'name' => 'Grafico Pizza', 'center' => $position, 'size' => 100, 'showInLegend' => false, 'dataLabels' => array('enabled' => false),'data' => $json));
		
	}
	
	/**
	 * <pre>
	 * Retorna o array com todos os valores setados, formatados corretamente para gerar o json 
	 * e criar o grafico de acordo com o tipo de grafico.
	 * Esta funcao é usada internamente no momento de criar o grafico.
	 * </pre>
	 * @return array Array para criacao do json
	 */
	public function getValoresJson(){
		
		
		$graficos = $this->getCombination();
		
		if(empty($graficos)){
			switch ($this->getTipoGrafico()){
				case self::GRAF_AREA:
				case self::GRAF_COLUNA:
				case self::GRAF_BARRA:
				case self::GRAF_LINHA:
					return $this->getColumLineSerie($this->getValores());
					break;
				case self::GRAF_PIZZA:
					return $this->getPieSerie($this->getValores());
					break;
			}
		}else{
			$arrSeries = array();
			
			$graficos = array_unique(array_merge($graficos,array($this->getTipoGrafico())));

			foreach($graficos as $comb){
				
				switch ($comb){
					case self::GRAF_COLUNA:
					case self::GRAF_BARRA:
						$arrSeries = array_merge($arrSeries,$this->getColumLineSerie($this->getValores(),$comb));
						break;
					case self::GRAF_LINHA:
					case self::GRAF_AREA:
						$arrSeries = array_merge($arrSeries,$this->getColumLineSerie($this->getValorSerie($this->getValores(),'media'),$comb));
						break;
					case self::GRAF_PIZZA:
						$arrSeries = array_merge($arrSeries,$this->getPieSerie($this->getValores(),$comb));
						break;
				}
				
			}
			
			return $arrSeries;

		}
	}
	

	/**
	 * <pre>&nbsp;
	 * Verifica se as variaveis setadas estao corretas para renderizar o grafico e caso 
	 * tenha ocorrido algum problema, mostra o erro
	 * </pre>
	 * @param boolean $retorna Informa se deve retornar a string ou printa-la
	 * @example exemplos/draw.php Arquivo de Exemplo
	 */
	public function draw($retorna = true){
		$error = $this->verificaParametros();
		if(count($error['erros']) > 0){
			$this->showErro($error);
		}else{
			
			$jsCod = "<script>";
			$jsCod .= "var chart;";
			$jsCod .= "$(function(){";
				$jsCod .= "chart = new Highcharts.Chart(";
					$jsCod .= "{";
						$jsCod .= "chart:".$this->getChartJson().",";
						$jsCod .= "title:".json_encode($this->getTitle()).",";
						$jsCod .= "subtitle:".json_encode($this->getSubtitle()).",";
						$jsCod .= "xAxis:".json_encode(array('categories' => $this->getCategoria())).",";
						$jsCod .= "yAxis:".json_encode($this->getYAxis()).",";
						$jsCod .= "legend:".json_encode($this->getLegend()).",";
						$jsCod .= "tooltip:".$this->getTooltipJson().",";
						$jsCod .= "plotOptions:".$this->getPlotOptionsJson().",";
						$jsCod .= "series:".json_encode($this->getValoresJson()).",";
						$jsCod .= "credits:".json_encode($this->getCredits())."";
					$jsCod .= "}";
				$jsCod .= ")";
			$jsCod .= "});";
			$jsCod .= "</script>";
			
//			print $jsCod;exit;
			
			if($retorna){
				return $jsCod;
			}else{
				echo $jsCod;
				return true;
			}
		}	
	}
	
	/**
	 * <pre>&nbsp;
	 * Verifica se os valores e categorias esto certo, ou seja, possuem o mesmo numero 
	 * de variaveis e verifica se é necessario esconder um valor.
	 * Funcao usada internamente pela {@link draw()}
	 * </pre>
	 * @return array Array com os erros caso exista algum.
	 */
	private function verificaParametros(){
		
		$valores = $this->getValores();
		$arrError = array();
		$numValores = array();
		$qtd = count($this->getCategoria());
		$mensagemErro = '';
		$graficos = $this->getCombination();
		$valorHide = $this->getValToHide();
		
		$max = 0;
		$min = 0;
		$i = 0;
		$hide = '';
		foreach($valores as $name => $data){
			$num = count($data);

			foreach ($data as $valMax)
			
				if($valMax > $max){
					$max = $valMax;
					$hide = $name;
				}
				
				if($i == 0){
					$min = $valMax;
				}else{
					if($valMax < $min){
						$min = $valMax;
					}
				}
				
				
				if($num != $qtd){
					$mensagemErro = 'Todas as categorias devem ter a mesma quantidade de valores - '.$qtd;
					$arrError[$name] = count($data);
				}
				
				$i = 1;
		}
		if($valorHide != 0){
			if($max > $min + $valorHide)
				$this->hideValue($hide);
		}
		
		$graficos = $this->getCombination();
		array_push($graficos, $this->getTipoGrafico());
		
		if(array_search(self::GRAF_BARRA, $graficos) !== FALSE && array_search(self::GRAF_COLUNA, $graficos) !== FALSE){
			$mensagemErro = 'Não é possível combinar os graficos: Barra e Coluna';
			$arrError[self::GRAF_BARRA] = 1;
			$arrError[self::GRAF_COLUNA] = 1;
		}
		
		if(array_search(HighchartsOptions::GRAF_PIZZA, $graficos) !== FALSE)
			$this->setYAxis(array('max' => $max * 2 ));
		
		return array('erros' => $arrError, 'qtd' => $qtd, 'mensagem' => $mensagemErro);
	}
	
	/**
	 * <pre>
	 * Reseta todas as opcoes utilizadas anteriormente.
	 * </pre>
	 * @example exemplos/resetOptions.php Arquivo de Exemplo
	 */
	public function resetOptions(){
		
		$this->resetDefault();
		unset($this->series);$this->series = array();
		unset($this->categories);$this->categories= array();
		
	}
	
	/**
	 * <pre>&nbsp;
	 * Mostra o erro occorido no momento de criação do grafico
	 * </pre>
	 * @param array $arrErro
	 */
	public function showErro($arrErro){
		
		echo "<div class='chart_error'>";
			echo "**********************************************************************************";
			echo "<p>".$arrErro['mensagem']."</p>";
			echo "<ul>";
				foreach($arrErro['erros'] as $ind => $err){
					echo "<li>".$ind." possui ".$err."</li>";
				}
			echo "</ul>";
			echo "**********************************************************************************";
		echo "</div>";
		
	}
	
	/**
	 * <pre>&nbsp;
	 * Retorna a soma de um array, ou a media do mesmo.
	 * Quando montado um grafico de PIZZA e for setado mais de um valor para cada categoria, 
	 * a funcao soma os valores ja que para esse tipo de grafiso eh permitido um valor para cada categoria.
	 * 
	 * Quando feita a combinacao de graficos e eh utilizado o grafico de linha selecionado 
	 * juncao RELACIONADA, é feita a media dos valores para mostrar no grafico.
	 * 
	 * funcao utilizada em {@link getPieSerie()} e {@link getValoresJson()}
	 * </pre>
	 * @param array $arr Array com os valores
	 * @param string $tipo soma ou media
	 * @return array com os novos valores
	 */
	private function getValorSerie($arr,$tipo){
		$retorno = 0;
		if($tipo == 'soma'){
			$soma = 0;
			foreach($arr as $ind => $val){
				$soma += $val;
			}
			
			$retorno = $soma;
		}elseif($tipo == 'media'){
			$arrRetorno = array();
			$valorAux = array();
			foreach($arr as $ind => $val){
				
				foreach ($val as $indice => $valor){
					
					if(!isset($valorAux[$indice]))
						$valorAux[$indice] = $valor;
					else
						$valorAux[$indice] += $valor;
					
					
				}
				
			}
			$numVal = count($arr);
			
			foreach($valorAux as $indAux => $valAux){
				$valorAux[$indAux] = $valAux / $numVal;
			}
			
			$retorno = array("Media" => $valorAux);
		}
		return $retorno;
	}
	
	/**
	 * <pre>&nbsp;
	 * Converte todos os valores do array para float
	 * </pre>
	 * @param array $array Array com os valores a serem transformado.
	 * @return array Array com os novos valores
	 */
	public function parseArrFloat($array){
		
		foreach($array as $ind=> $val )
			$array[$ind] = floatval($val);
		
			return $array;
	}
	
}