<?php
/**
 * Classe responsável pelas configurações 
 * do grafico a ser gerado
 * 
 * @author 
 * @version 1.0
 * @package Highcharts
 */
class HighchartsOptions {

	const GRAF_COLUNA = 'column';
	const GRAF_BARRA = 'bar';
	const GRAF_LINHA = 'line';
	const GRAF_AREA = 'area';
	const GRAF_PIZZA = 'pie';
	
	const FUNC_CRIADA = 1;
	const FUNC_CRIAR = 0;
	
	const FUNCAO_GRAFICO = 'chart_func';
	const FUNCAO_PONTO_GRAF = 'plot_func';
	const FUNCAO_TOOLTIP = 'tooltip_func';
	const FUNCAO_LABEL = 'label_func';
	
	const COMB_SIMPLES = 'simple';
	const COMB_RELACIONADA = 'relacionada';
	
	/**
	 * Cores usadas para as linhas, colunas, etc...
	 */
	private $colors = array();
	
	/**
	 * Simbolos usados para marcar os pontos no grafico
	 */
	private $symbols = array();
	
	/**
	 * Idioma
	 */
	private $lang = array();
	
	/**
	 * Propriedades especificas do grafico
	 */
	private $chart = array();

	/**
	 * Seta as funções a serem executadas em eventos, especificados, do grafico
	 */
	private $functions = array();
	
	/**
	 * Titulo do grafico
	 */
	private $title = array();
	
	/**
	 * Subtitulo do grafico(localizado logo abaixo do titulo)
	 */
	private $subtitle = array();
	
	/**
	 * Titulo para o eixo Y.
	 */
	private $yAxis = array();
	
	/**
	 * Opções dos pontos
	 */
	private $plotOptions = array();
	
	/**
	 * Propriedades dos labels
	 */
	private $labels = array();
	
	/**
	 * Propriedades das legendas
	 */
	private $legend = array();
	
	/**
	 * Propriedades do tooltip
	 */
	private $tooltip = array();
	
	/**
	 * Estilo da toolbar
	 */
	private $toolbar = array();
	
	/**
	 * Credito do grafico
	 */
	private $credits = array();
	
	/**
	 * Inicia as variaveis com seus valores default
	 */
	public function __construct(){

		/**
		 * Setando os valores default para as propriedades
		 * 
		 */
		$this->chart = $this->getDefault('chart');
		$this->colors = $this->getDefault('colors');
		$this->credits = $this->getDefault('credits');
		$this->labels = $this->getDefault('labels');
		$this->lang = $this->getDefault('lang');
		$this->legend = $this->getDefault('legend');
		$this->plotOptions = $this->getDefault('plotOptions');
		$this->subtitle = $this->getDefault('subtitle');
		$this->yAxis = $this->getDefault('yAxis');
		$this->symbols = $this->getDefault('symbols');
		$this->title = $this->getDefault('title');
		$this->toolbar = $this->getDefault('toolbar');
		$this->tooltip = $this->getDefault('tooltip');
		
	}
	
	/**
	 * 
	 * <pre>&nbsp;
	 * Inclui cores ao array defult;
	 * 
	 * Obs.: caso seja passado mais de uma vez a mesma cor, será ioncluido somente uma vez.
	 * </pre>
	 * @param string|array $colors Cores a serem incluidas
	 * @example exemplos/setColors.php Arquivo de Exemplo
	 */
	public function setColors($colors){
		
		$default = $this->getColors();
		
		if(!is_array($colors)){
			$colors = array($colors);
		}
		
		$this->colors = array_unique(array_merge($colors,$default));
		
	}
	
	/**
	 * 
	 * <pre>&nbsp;
	 * Adiciona simbolos a serem usados no grafico;
	 * 
	 * Obs.: Caso seja adicionado mais de uma vez o mesmo simbolo, sera ignorado a segunda ocorrência 
	 * 
	 * </pre>
	 * @param string:array $symbols Simbolo a ser adicionada no array
	 * @example exemplos/setSymbols.php Arquivo de Exemplo
	 */
	public function setSymbols($symbols){

		$default = $this->getSymbols();
		
		if(!is_array($symbols)){
			$symbols = array($symbols);
		}
		
		$this->symbols = array_unique(array_merge($symbols,$default));
		
	}
	
	/**
	 * 
	 * <pre>&nbsp;
	 * Seta um idioma para as legendas criadas.
	 * 
	 * Idiomas disponiveis:
	 * 	eng - Ingles.
	 * 	pt-br - Portugues(Brasil).
	 * </pre>
	 * @param string $lang Idioma desejado
	 * @example exemplos/setLang.php Arquivo de Exemplo
	 */
	public function setLang($lang){
		
		$this->lang = $this->getLanguage($lang);
		
	}
	
	/**
	 * <pre>&nbsp;
	 * Seta o valor para as propriedades do grafico.
	 * 
	 * Possiveis parametros
	 *	'renderTo' => 'container'
	 *	'borderColor' => '#4572A7',
	 *	'borderRadius' => 5,		
	 *	'defaultSeriesType'  => 'line',
	 *	'ignoreHiddenSeries' => true,
	 *	'spacingTop' => 10,
	 *	'spacingRight' => 10,
	 *	'spacingBottom' => 15,
	 *	'spacingLeft' => 10,
	 *	'style' => array(
	 *		'fontFamily' => '"Lucida Grande", "Lucida Sans Unicode", Verdana, Arial, Helvetica, sans-serif',
	 *		'fontSize' => '12px'
	 *	),
	 *	'backgroundColor' => '#FFFFFF',
	 *	'plotBorderColor' => '#C0C0C0'
	 * </pre>
	 * @param array $chart <p>Array com os novos parametros</p>
	 * @example exemplos/setChart.php Arquivo de Exemplo
	 */
	public function setChart($chart){
		
		$default = $this->getChart();
		
		$this->chart = $this->mergeOptions($default,$chart);
		
	}
	
	/**
	 * <pre>&nbsp;
	 * Informa qual funcao js deve ser executada em um determinado momento.
	 * 	
	 * Parametros
	 * 	propriedade:
	 * 		FUNCAO_GRAFICO;
	 *		FUNCAO_PONTO_GRAF;
	 *		FUNCAO_TOOLTIP;
	 *		FUNCAO_LABEL;
	 * 	array(
	 * 		'evento' => array(
	 * 			'funcao' => '(nome da funcao, ou o script da funcao caso o tipo de funcao seja FUNC_CRIAR)',
	 * 			'tipo'   => Highcharts::FUNC_CRIADA ou Highcharts::FUNC_CRIAR 
	 * 		)
	 * 	)
	 * 
	 * Para: 
	 * 	FUNC_CRIADA, deve ser passadp o nome da funcao ja criada no js;
	 * 	FUNC_CRIAR, deve ser passado o script da funcao sem a declaracao.
	 * 
   	 * possiveis evendos:
   	 * 	Chart
   	 * 		load, 
	 * 		click, 
	 * 		redraw, 
	 * 		selection, 
	 * 		addSeries
	 * 
	 * 	Plot
	 * 	 	click,
	 * 		mouseOver,
	 * 		mouseOut,
	 * 		remove,
	 * 		select,
	 * 		unselect,
	 * 		update
	 * 
	 * 	Tooltip/Lable
	 * 		formatter
	 * </pre>
	 * @param string $propriedade define da propriedade
	 * @param array $functions Array com os novos parametros
	 * @example exemplos/setFunctions.php Arquivo de Exemplo
	 */
	public function setFunctions($propriedade, $functions){

		if(!isset($this->functions[$propriedade]))
			$this->functions[$propriedade] = array();
			
		foreach($functions as $event => $func){
			array_push($this->functions[$propriedade], array('event' => $event, 'funcao' => $func));
		}
		
	}
	
	/**
	 * <pre>$nbsp;
	 * Seta o valor para as propriedades do titulo do grafico.
	 * 
	 * Possiveis parametros
	 *	'text' => 'Título',
	 *	'align' => 'center',
	 *	'y' => 15,
	 *	'style' => array(
	 *		'color' => '#3E576F',
	 *		'fontSize' => '16px'
	 *	)
	 * </pre>
	 * @param array $title Array com os novos parametros
	 * @example exemplos/setTitle.php Arquivo de Exemplo
	 */
	public function setTitle($title){
		
		$default = $this->getTitle();
		
		$this->title =  $this->mergeOptions($default,$title);
		
	}
	
	/**
	 * <pre>&nbsp;
	 * Seta o valor para as propriedades do subtitulo do grafico.
	 * 
	 * Possiveis parametros
	 *	'text' => 'Título',
	 *	'align' => 'center',
	 *	'y' => 15,
	 *	'style' => array(
	 *		'color' => '#3E576F',
	 *		'fontSize' => '16px'
	 *	)
	 * </pre>
	 * @param array $subtitle Array com os novos parametros
	 * @example exemplos/setSubtitle.php Arquivo de Exemplo
	 */
	public function setSubtitle($subtitle){
		
		$default = $this->getSubtitle();
		
		$this->subtitle = $this->mergeOptions($default,$subtitle);
		
	}
	
	/**
	 * <pre>&nbsp;
	 * Seta o valor para as propriedades do titulo no eixo Y, deve ser passado como parametro um array com as novas propriedades
	 * 
	 * Possiveis parametros
	 *	'title' => array(
	 *  	'text' => 'Valores Y'
	 *  ),
	 *  'plotLines' => array(
	 *  	array(
	 *	    	'value' => 0,
	 *	        'width' => 1,
	 *	        'color' => '#808080'
	 *	    )
	 *	)
	 * <pre>
	 * @param array $subtitle Array com os novos parametros
	 * @example exemplos/setYAxis.php Arquivo de Exemplo
	 */
	public function setYAxis($yAxis){
		
		$default = $this->getYAxis();
		
		$this->yAxis = $this->mergeOptions($default,$yAxis);
		
	}
	
	/**
	 * <pre>&nbsp;
	 * Seta o valor para as propriedades do ponto no grafico
	 * 
	 * Possiveis parametros
	 *	'allowPointSelect' => false,
	 *	'showCheckbox' => false,
	 *	'animation' => array(
	 *		'duration' => 1000
	 *	),
	 *	'events' => array(),
	 *	'lineWidth' => 2,
	 *	'shadow' => true,
	 *	'marker' => array( 
	 *		'enabled' => true,
	 *		'ineWidth' => 0,
	 *		'radius' => 4,
	 *		'lineColor' => '#FFFFFF',
	 *		'states' => array(
	 *			'hover' => array(),
	 *			'select' => array(
	 *				'fillColor' => '#FFFFFF',
	 *				'lineColor' => '#000000',
	 *				'lineWidth' => 2
	 *				)					
	 *			)
	 *		),
	 *	'point' => array(
	 *		'events' => array()
	 *	),
	 *	'showInLegend' => true,
	 *	'states' => array(
	 *		'hover' => array(
	 *			'marker' => array(
	 *			)
	 *		),
	 *		'select' => array(
	 *			'marker' => array()
	 *		)
	 *	),
	 *	'stickyTracking' => true 
	 * </pre>
	 * @param array $subtitle Array com os novos parametros
	 * @example exemplos/setPlotOptions.php Arquivo de Exemplo
	 */
	public function setPlotOptions($plotOptions){
		
		$default = $this->getPlotOptions();

		$this->plotOptions = $this->mergeOptions($default,$plotOptions);

	}
	
	/**
	 * <pre>&nbsp;
	 * Seta o valor para as propriedades do label do grafico
	 * 
	 * Possiveis parametros
	 *	'style' => array(
	 *		'position' => 'absolute',
	 *		'color' => '#3E576F'
	 *	)
	 * </pre>
	 * @param array $labels Array com os novos parametros
	 * @example exemplos/setLabels.php Arquivo de Exemplo
	 */
	public function setLabels($labels){
			
		$default = $this->getLabels();
		
		$this->labels = $this->mergeOptions($default,$labels);
		
	}
	
	/**
	 * <pre>
	 * Seta o valor para as propriedades da legenda do grafico
	 * 
	 * Possiveis parametros
	 *	'enabled' => true,
	 *	'align' => 'center',
	 *	'layout' => 'horizontal',
	 *	'borderWidth' => 1,
	 *	'borderColor' => '#909090',
	 *	'borderRadius' => 5,
	 *	'shadow' => false,
	 *	'style' => array(
	 *		'padding' => '5px'
	 *	),
	 *	'itemStyle' => array(
	 *		'cursor' => 'pointer',
	 *		'color' => '#3E576F'
	 *	),
	 *	'itemHoverStyle' => array(
	 *		'cursor' => 'pointer',
	 *		'color' => '#000000'
	 *	),
	 *	'itemHiddenStyle' => array(
	 *		'color' => '#C0C0C0'
	 *	),
	 *	'itemCheckboxStyle' => array(
	 *		'position' => 'absolute',
	 * 		'width' => '13px',
	 *		'height' => '13px'
	 *	),
	 *	'symbolWidth' => 16,
	 *	'symbolPadding' => 5,
	 *	'verticalAlign' => 'bottom',
	 *	'x' => 0, 
	 *	'y' => 0
	 * </pre>
	 * @param array $legend Array com os novos parametros
	 * @example exemplos/setLegend.php Arquivo de Exemplo
	 */
	public function setLegend($legend){
			
		$default = $this->getLegend();
		
		$this->legend = $this->mergeOptions($default,$legend);
		
	}
	
	/**
	 * <pre>&nbsp;
	 * Seta o valor para as propriedades do tooltip do grafico.
	 * 
	 * Possiveis parametros
	 * 	'enabled' => true,
	 *	'backgroundColor' => 'rgba(255, 255, 255, .85)',
	 *	'borderWidth' => 2,
	 *	'borderRadius' => 5,
	 *	'shadow' => true,
	 *	'style' => array(
	 *		'color' => '#333333',
	 *		'fontSize' => '12px',
	 *		'padding' => '5px',
	 *		'whiteSpace' => 'nowrap'
	 *	)
	 * </pre>
	 * @param array $tooltip Array com os novos parametros
	 * @example exemplos/setTooltip.php Arquivo de Exemplo
	 */
	public function setTooltip($tooltip){
			
		$default = $this->getTooltip();
		
		$this->tooltip = $this->mergeOptions($default,$tooltip);
		
	}
	
	/**
	 * <pre>&nbsp;
	 * Seta o valor para as propriedades da toolbar do grafico.
	 * 
	 * Possiveis parametros
	 * 	itemStyle => array(
	 *		color: '#4572A7',
	 *		cursor: 'pointer'
	 *	)
	 * </pre>
	 * @param array $toolbar Array com os novos parametros
	 * @example exemplos/setToolbar.php Arquivo de Exemplo
	 */
	public function setToolbar($toolbar){
			
		$default = $this->getToolbar();
		
		$this->toolbar = $this->mergeOptions($default,$toolbar);
		
	}
	
	/**
	 * <pre>&nbsp;
	 * Seta os creditos do grafico.
	 * 
	 * Possiveis parametros
	 * 	'enabled' => true,
	 *	'text' => 'Finnet Brasil',
	 *	'href' => 'http =>//www.finnetbrasil.com.br',
	 *	'position' => array(
	 *		'align' => 'right',
	 *		'x' => -10,
	 *		'verticalAlign' => 'bottom',
	 *		'y' => -5
	 *	),
	 *	'style' => array( 
	 *		'cursor' => 'pointer',
	 *		'color' => '#909090',
	 *		'fontSize' => '10px'
	 *	)
	 * </pre>
	 * @param array $credits Array com os novos parametros
	 * @example exemplos/setCredits.php Arquivo de Exemplo
	 */
	public function setCredits($credits){
			
		$default = $this->getToolbar();
		
		$this->toolbar = $this->mergeOptions($default,$credits);
		
	}
	
	/**
	 * <pre>&nbsp;
	 * Retorna o array com as cores setadas para utilização
	 * 
	 * @return array Array com as cores
	 * @example exemplos/getSimples.php Arquivo de Exemplo
	 * </pre>
	 */
	public function getColors(){
		
		return $this->colors;
			
	}
	
	/**
	 * <pre>&nbsp;
	 * Retorna o array com os simbolos setados para utilização
	 * 
	 * @return array Array com os simbolos
	 * @example exemplos/getSimples.php Arquivo de Exemplo
	 * </pre>
	 */
	public function getSymbols(){
		
		return $this->symbols;
			
	}
	
	/**
	 * <pre>&nbsp;
	 * Retorna o array das propriedades do grafico ou o valor de uma propriedade 
	 * passada por parametro, por default é retornado o array completo.
	 * 
	 * Parametro
	 * 	todos os parametros setados em {@link setChart()}
	 * </pre>
	 * @param string $param O nome da propriedade.
	 * @return string|array
	 * @example exemplos/getChart.php Arquivo de Exemplo
	 */
	public function getChart($param = ''){
		
		if($param == '')
			return $this->chart;
		else
			return $this->returnParamArray($this->chart, $param);
			
	}
	
	/**
	 * <pre>&nbsp;
	 * Retorna o array com todas as funcoes setadas ou as funcoes de uma propriedade 
	 * especifica, caso seja passado a propriedade por parametro 
	 * 
	 * Parametro
	 * 	default - ''.
	 *	FUNCAO_GRAFICO;
	 *	FUNCAO_PONTO_GRAF;
	 *	FUNCAO_TOOLTIP;
	 *	FUNCAO_LABEL;
	 * </pre>
	 * @param string $propriedade O nome da propriedade.
	 * @example exemplos/getFunctions.php Arquivo de Exemplo
	 */
	public function getFunctions($propriedade = ''){
		
		if($propriedade == '')
			return $this->functions;
		else
			return $this->returnParamArray($this->functions, $propriedade);
		
	}
	
	/**
	 * <pre>&nbsp;
	 * Retorna o array das propriedades do titulo ou o valor de uma propriedade 
	 * passada por parametro
	 * 
	 * Parametro
	 * 	todos os parametros setados em {@link setTitle()}
	 * </pre>
	 * @param string $param O nome da propriedade.
	 * @example exemplos/getTitle.php Arquivo de Exemplo
	 */
	public function getTitle($param = ''){
		
		if($param == '')
			return $this->title;
		else
			return $this->returnParamArray($this->title, $param);
			
	}
	
	/**
	 * <pre>&nbsp;
	 * Retorna o array das propriedades do subtitulo ou o valor de uma propriedade 
	 * passada por parametro
	 * 
	 * Parametro
	 * 	todos os parametros setados em {@link setSubtitle()}
	 * </pre>
	 * @param string $param O nome da propriedade.
	 * @example exemplos/getSubtitle.php Arquivo de Exemplo
	 */
	public function getSubtitle($param = ''){
		
		if($param == '')
			return $this->subtitle;
		else
			return $this->returnParamArray($this->subtitle, $param);
			
	}
	
	/**
	 * <pre>&nbsp;
	 * Retorna o array das propriedades do eixo Y ou o valor de uma propriedade 
	 * passada por parametro
	 * 
	 * Parametro
	 * 	todos os parametros setados em {@link setYAxis()}
	 * </pre>
	 * @param string $param O nome da propriedade.
	 * @example exemplos/getYAxis.php Arquivo de Exemplo
	 */
	public function getYAxis($param = ''){
		
		if($param == '')
			return $this->yAxis;
		else
			return $this->returnParamArray($this->yAxis, $param);
			
	}
	
	/**
	 * <pre>&nbsp;
	 * Retorna o array das propriedades dos pontos no grafico ou o valor de uma 
	 * propriedade passada por parametro
	 * 
	 * Parametro
	 * 	todos os parametros setados em {@link setPlotOptions()}
	 * </pre>
	 * @param string $param O nome da propriedade.
	 * @example exemplos/getPlotOptions.php Arquivo de Exemplo
	 */
	public function getPlotOptions($param = ''){

		if($param == '')
			return $this->plotOptions;
		else
			return $this->returnParamArray($this->plotOptions, $param);
			
	}
	
	/**
	 * <pre>&nbsp;
	 * Retorna o array das propriedades do label ou o valor de uma propriedade 
	 * passada por parametro
	 * 
	 * Parametro
	 * 	todos os parametros setados em {@link setLabels()}
	 * </pre>	
	 * @param string $param O nome da propriedade.
	 * @example exemplos/getLabels.php Arquivo de Exemplo
	 */
	public function getLabels($param = ''){
		
		if($param == '')
			return $this->labels;
		else
			return $this->returnParamArray($this->labels, $param);
			
	}
	
	/**
	 * <pre>&nbsp;
	 * Retorna o array das propriedades da legenda ou o valor de uma propriedade 
	 * passada por parametro
	 * 
	 * Parametro
	 * 	todos os parametros setados em {@link setLegend()}
	 * 		
	 * @param string $param O nome da propriedade.
	 * @example exemplos/getLegend.php Arquivo de Exemplo
	 */
	public function getLegend($param = ''){
		
		if($param == '')
			return $this->legend;
		else
			return $this->returnParamArray($this->legend, $param);
			
	}
	
	/**
	 * <pre>&nbsp;
	 * Retorna o array das propriedades do tooltip ou o valor de uma propriedade 
	 * passada por parametro
	 * 
	 * Parametro
	 * 	todos os parametros setados em {@link setTooltip()}
	 * </pre>
	 * @param string $param <p>O nome da propriedade.</p>
	 * @example exemplos/getTooltip.php Arquivo de Exemplo
	 */
	public function getTooltip($param = ''){
		
		if($param == '')
			return $this->tooltip;
		else
			return $this->returnParamArray($this->tooltip, $param);
			
	}
	
	/**
	 * <pre>&nbsp;
	 * Retorna o array das propriedades da toolbar ou o valor de uma propriedade 
	 * passada por parametro
     *
	 * Parametro
	 * 	todos os parametros setados em {@link setToolbar()}
	 * </pre>
	 * @param string $param O nome da propriedade.
	 * @example exemplos/getToolbar.php Arquivo de Exemplo
	 */
	public function getToolbar($param = ''){
		
		if($param == '')
			return $this->toolbar;
		else
			return $this->returnParamArray($this->toolbar, $param);
			
	}
	
	/**
	 * <pre>&nbsp;
	 * Retorna o array das propriedades dos creditos ou o valor de uma propriedade 
	 * passada por parametro
	 * 
	 * Parametro
	 * 	todos os parametros setados em {@link setCredits()}
	 * </pre>
	 * @param string $param O nome da propriedade.
	 * @example exemplos/getCredits.php Arquivo de Exemplo
	 */
	public function getCredits($param = ''){
		
		if($param == '')
			return $this->credits;
		else
			return $this->returnParamArray($this->credits, $param);
			
	}
	
	/**
	 * <pre>&nbsp;
	 * getChartJson é uma funcao utilizada internamente para retornar o jason das opções do 
	 * grafico, assim é possível incluir funcoes caso necessario
	 * 
	 * @return string Json do charts
	 * </pre>
	 */
	public function getChartJson(){
		$functions  = $this->getFunctions(self::FUNCAO_GRAFICO);
		$funcJson = '';
		$json = "{";
			$json .= "'renderTo' : '".$this->getChart('renderTo')."',";
			$json .= "'inverted' : '".$this->getChart('inverted')."',";
			$json .= "'borderColor' : '".$this->getChart('borderColor')."',";
			$json .= "'borderRadius' : ".$this->getChart('borderRadius').",";
			$json .= "'defaultSeriesType' : '".$this->getChart('defaultSeriesType')."',";
			$json .= "'ignoreHiddenSeries' : ".$this->getChart('ignoreHiddenSeries').",";
			$json .= "'spacingTop' : ".$this->getChart('spacingTop').",";
			$json .= "'spacingRight' : ".$this->getChart('spacingRight').",";
			$json .= "'spacingBottom' : ".$this->getChart('spacingBottom').",";
			$json .= "'spacingLeft' : ".$this->getChart('spacingLeft').",";
			if(!empty($functions)){
				$json .= "'events':{";
					foreach ($functions as $arrFunc){
						
						$event = $arrFunc['event'];
						$funcName = $arrFunc['funcao'];
						
						if($funcName['tipo'] == self::FUNC_CRIADA)
							$funcJson .= "'".$event."':function(){".$funcName['funcao'].".call()},";
						else
							$funcJson .= "'".$event."':function(event){".$funcName['funcao']."},";
					}
				$json .= rtrim($funcJson,',')."},";
			}
			$json .= "'style' : { 'fontFamily': '".addslashes($this->getChart('fontFamily'))."', 'fontSize': '".$this->getChart('fontSize')."'},";
			$json .= "'backgroundColor' : '".$this->getChart('backgroundColor')."',";
			$json .= "'plotBorderColor' : '".$this->getChart('plotBorderColor')."',";
			$json .= "'zoomType' : '".$this->getChart('zoomType')."'";
		$json .= "}";
		
		return $json;
	}
	
	/**
	 * <pre>&nbsp;
	 * Monta o json dos pontos do grafico, assim é possivel incluir uma funcao nos eventos 
	 * referente ao ponto no grafico( click,mouseOver,mouseOut,remove,select,unselect,update )
	 * </pre>
	 * @return string Json dos pontos no grafico
	 */
	public function getPlotOptionsJson(){
		$functions  = $this->getFunctions(self::FUNCAO_PONTO_GRAF);
		$funcJson = '';
		
		$labelFunctions = $this->getFunctions(self::FUNCAO_LABEL);
		if(empty($labelFunctions))
			$labelFunctions = $this->getDefault('label_'.self::GRAF_PIZZA);

		$pie = $this->getPlotOptions('pie');

		$json = "{";
		
			$json .= "'pie' : {";
					$json .= "'allowPointSelect' : '".$pie['allowPointSelect']."',";
					$json .= "'dataLabels' : {";
						$json .= "'enabled' : '".$pie['dataLabels']['enabled']."',";
						if(!empty($labelFunctions)){
						
							$labFormatter = $labelFunctions[0];				
							$event = $labFormatter['event'];
			
							if($labFormatter['funcao']['tipo'] == self::FUNC_CRIADA)
								$json .= "'".$event."':function(){".$labFormatter['funcao']['funcao'].".call()},";
							else
								$json .= "'".$event."':function(event){".$labFormatter['funcao']['funcao']."},";
							
						}
					$json .= "},";
					$json .= "'showInLegend' : '".$pie['showInLegend']."'";
			$json .= "},";
			
			$json .= "'allowPointSelect' : '".$this->getPlotOptions('allowPointSelect')."',";
			$json .= "'showCheckbox' : '".$this->getPlotOptions('showCheckbox')."',";
			$json .= "'animation' : '".json_encode($this->getPlotOptions('animation'))."',";
			if(!empty($functions)){
				$json .= "'series': {";
					$json .= "'point': {";
						$json .= "'events':{";
							foreach ($functions as $arrFunc){
								
								$event = $arrFunc['event'];
								$funcName = $arrFunc['funcao'];
						
								if($funcName['tipo'] == self::FUNC_CRIADA)
									$funcJson .= "'".$event."':function(){".$funcName['funcao'].".call()},";
								else
									$funcJson .= "'".$event."':function(event){".$funcName['funcao']."},";
							}
						$json .= rtrim($funcJson,',')."}";
					$json .= "}";
				$json .= "},";
			}			
			$json .= "'events' : '".json_encode($this->getPlotOptions('events'))."',";
			$json .= "'lineWidth' : '".$this->getPlotOptions('lineWidth')."',";
			$json .= "'shadow' : '".$this->getPlotOptions('shadow')."',";
			$json .= "'marker' : '".json_encode($this->getPlotOptions('marker'))."',";
			$json .= "'point' : '".json_encode($this->getPlotOptions('point'))."',";
			$json .= "'showInLegend' : '".$this->getPlotOptions('showInLegend')."',";
			$json .= "'states' : '".json_encode($this->getPlotOptions('states'))."',";
			$json .= "'stickyTracking' : '".$this->getPlotOptions('stickyTracking')."',";
			$json .= "'column' : ".json_encode($this->getPlotOptions('column'))."";
		$json .= "}";

		return $json;
	}
	
	/**
	 * <pre>&nbsp;
	 * Monta o json do tooltip, assim é possivel setar uma funcao especifica.
	 * </pre>
	 * @return string Json do tooltip
	 */
	public function getTooltipJson(){
		
		$functions  = $this->getFunctions(self::FUNCAO_TOOLTIP);
		$funcJson = '';
		
		if(empty($functions))
			$functions = $this->getDefault('func_tooltip_'.$this->getChart('defaultSeriesType'));
		
		
		$json = "{";
			$json .= "'enabled' : '".$this->getTooltip('enabled')."',";
			$json .= "'backgroundColor' : '".$this->getTooltip('backgroundColor')."',";
			$json .= "'borderWidth' : ".$this->getTooltip('borderWidth').",";
			$json .= "'borderRadius' : ".$this->getTooltip('borderRadius').",";
			$json .= "'shadow' : '".$this->getTooltip('borderWidth')."',";
			if(!empty($functions)){
			
				$formatter = $functions[0];				
				$event = $formatter['event'];

				if($formatter['funcao']['tipo'] == self::FUNC_CRIADA)
					$json .= "'".$event."':function(){".$formatter['funcao']['funcao'].".call()},";
				else
					$json .= "'".$event."':function(event){".$formatter['funcao']['funcao']."},";
				
			}
			$json .= "'style' : ".json_encode($this->getTooltip('style')).",";
		$json .= "}";
		
		return $json;
	}
	
	/**
	 * <pre>&nbsp;
	 * Retorna os valores padrao para cada atributo passado por parametro ou retorna uma funcao default.
	 * 
	 * Parametros para atributos
	 * 	colors
	 * 	symbols
	 * 	lang
	 * 	chart
	 * 	title
	 * 	subtitle
	 * 	yAxis
	 * 	plotOptions
	 * 	labels
	 * 	legend
	 * 	tooltip
	 * 	toolbar
	 * 	credits
	 * 
	 * Parametros para funcoes
	 * 	func_tooltip_column
	 *	func_tooltip_bar
	 *	func_tooltip_line
	 *	func_tooltip_area
	 *	func_tooltip_pie
	 *	label_pie
	 * 
	 * </pre>
	 * @access private
	 * @param string $param <p>O parametro que deve ser retornado os valores</p>
	 * @return multitype:string
	 * @example exemplos/getDefault.php Arquivo de Exemplo
	 */
	private function getDefault($param){
		
		switch ($param){
			case 'colors':
				return array('#4572A7', '#AA4643', '#89A54E', '#80699B', '#3D96AE','#DB843D', '#92A8CD', '#A47D7C', '#B5CA92');
				break;
			case 'symbols':
				return array('circle', 'diamond', 'square', 'triangle', 'triangle-down');
				break;
			case 'lang':
				return $this->getLanguage('pt-br');
				break;
			case 'chart':
				return array(
						'renderTo' => 'container',
						'borderColor' => '#4572A7',
						'borderRadius' => 5,		
						'defaultSeriesType'  => 'spline',
						'inverted' => false,
						'ignoreHiddenSeries' => 'true',
						'spacingTop' => 10,
						'spacingRight' => 10,
						'spacingBottom' => 15,
						'spacingLeft' => 10,
						'style' => array(
							'fontFamily' => '"Lucida Grande", "Lucida Sans Unicode", Verdana, Arial, Helvetica, sans-serif',
							'fontSize' => '12px'
							),
						'backgroundColor' => '#FFFFFF',
						'plotBorderColor' => '#C0C0C0',
						'zoomType' => 'xy'
					);
				break;	
			case 'title':
					return array(
						'text' => 'Título',
						'align' => 'center',
						'y' => 15,
						'style' => array(
								'color' => '#3E576F',
								'fontSize' => '12px'
							)
					);
				break;
			case 'subtitle':
					return array(
						'text' => '',
						'align' => 'center',
						'y' => 30,
						'style' => array(
								'color' => '#6D869F'
							)
					);
				break;
			case 'yAxis':
				return array(
							'max' => null,
							'title' => array(
					            'text' => 'Valores Y'
					         ),
					         'plotLines' => array(
					         	array(
						            'value' => 0,
						            'width' => 1,
						            'color' => '#808080'
						        )
					         )
						);
				break;
			case 'plotOptions':
				return array(
							'allowPointSelect' => 'true',
							'showCheckbox' => 'false',
							'animation' => array(
								'duration' => 1000
							),
							'events' => array(),
							'lineWidth' => 2,
							'shadow' => 'true',
							'marker' => array( 
								'enabled' => 'true',
								'ineWidth' => 0,
								'radius' => 4,
								'lineColor' => '#FFFFFF',
								'states' => array(
									'hover' => array(),
									'select' => array(
										'fillColor' => '#FFFFFF',
										'lineColor' => '#000000',
										'lineWidth' => 2
									)					
								)
							),
							'point' => array(
								'events' => array()
							),
							'showInLegend' => 'true',
							'states' => array(
								'hover' => array(
									'marker' => array(
									)
								),
								'select' => array(
									'marker' => array()
								)
							),
							'stickyTracking' => 'true',
							'pie' => array(
								'allowPointSelect' => 'true',
								'dataLabels' => array(
									'enabled' => 'false'
						        ),
						        'showInLegend' => 'true'
							),
							'column' => array(
					            'stacking' => null
					         )
						);
				break;
			case 'labels':
					return array('style' => array(
						'position' => 'absolute',
						'color' => '#3E576F'
						)
					);
				break;
			case 'legend':
				return array(
								'enabled' => 'true',
								'align' => 'center',
								'layout' => 'horizontal',
								'borderWidth' => 1,
								'borderColor' => '#909090',
								'borderRadius' => 5,
								'shadow' => false,
								'style' => array(
												'padding' => '5px'
											),
								'itemStyle' => array(
													'cursor' => 'pointer',
													'color' => '#3E576F'
												),
								'itemHoverStyle' => array(
														'cursor' => 'pointer',
														'color' => '#000000'
													),
								'itemHiddenStyle' => array(
														'color' => '#C0C0C0'
													),
								'itemCheckboxStyle' => array(
															'position' => 'absolute',
															'width' => '13px',
															'height' => '13px'
														),
								'symbolWidth' => 16,
								'symbolPadding' => 5,
								'verticalAlign' => 'bottom',
								'x' => 0, 
								'y' => 0
							);
				break;	
			case 'tooltip':
					return array(
							'enabled' => true,
							'backgroundColor' => 'rgba(255, 255, 255, .85)',
							'borderWidth' => 2,
							'borderRadius' => 5,
							'shadow' => true,
							'style' => array(
										'color' => '#333333',
										'fontSize' => '12px',
										'padding' => '5px',
										'whiteSpace' => 'nowrap'
									)
						);
				break;	
			case 'toolbar':
					return array(
							'itemStyle' => array(
								'color' => '#4572A7',
								'cursor'=> 'pointer'
							)
					);
				break;
//			case 'credits':
//				return array( 
//						'enabled' => true,
//						'text' => 'Finnet Brasil',
//						'href' => 'http://www.finnetbrasil.com.br',
//						'position' => array(
//							'align' => 'right',
//							'x' => -10,
//							'verticalAlign' => 'bottom',
//							'y' => -5
//						),
//						'style' => array( 
//							'cursor' => 'pointer',
//							'color' => '#909090',
//							'fontSize' => '10px'
//						)
//					);
//				break;		
			case 'func_tooltip_'.self::GRAF_COLUNA:
			case 'func_tooltip_'.self::GRAF_LINHA:				
			case 'func_tooltip_'.self::GRAF_BARRA:				
				return array(array('event' =>'formatter','funcao'=>array('funcao' => "return '<b>'+this.series.name +'</b>: '+ this.y;",'tipo' => Highcharts::FUNC_CRIAR)));
				break;	
			case 'func_tooltip_'.self::GRAF_PIZZA:
				return array(array('event' =>'formatter','funcao'=>array('funcao' => "return '<b>'+this.point.name +'</b>: '+ Highcharts.numberFormat(this.percentage,2) +' %';",'tipo' => Highcharts::FUNC_CRIAR)));
				break;
			case 'label_'.self::GRAF_PIZZA:
				return array(array('event' =>'formatter','funcao'=>array('funcao' => "return ''+ Highcharts.numberFormat(this.percentage,2) +' %';",'tipo' => Highcharts::FUNC_CRIAR)));
				break;		
			default:
				return '';
				break;
		}
		
	}
	
	/**
	 * <pre>&nbsp;
	 * Retorna o array com as legendas no idioma selecionado
	 * </pre>
	 * @access private
	 * @param string $lang
	 * @example exemplos/getDefault.php Arquivo de Exemplo
	 */
	private function getLanguage($lang){
		
		switch(strtolower($lang)){
			case 'pt-br':
				return array(
								'loading' => 'Loading...',
								'months' => array('Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'),
								'weekdays' => array('Segunda','Terça','Quarta','Quinta','Sexta'),
								'decimalPoint' => ',',
								'resetZoom' => 'Resetar zoom',
								'resetZoomTitle' => 'Resetar zoom 1:1',
								'thousandsSep' => '.'
							);
				break;
			case 'eng':
				return array(
								'loading' => 'Loading...',
								'months' => array('January', 'February', 'March', 'April', 'May', 'June', 'July','August', 'September', 'October', 'November', 'December'),
								'weekdays' => array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'),
								'decimalPoint' => '.',
								'resetZoom' => 'Reset zoom',
								'resetZoomTitle' => 'Reset zoom level 1:1',
								'thousandsSep' => ','
							);
				break;
			default:
				return array(
								'loading' => 'Loading...',
								'months' => array('Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'),
								'weekdays' => array('Segunda','Terça','Quarta','Quinta','Sexta'),
								'decimalPoint' => ',',
								'resetZoom' => 'Resetar zoom',
								'resetZoomTitle' => 'Resetar zoom 1:1',
								'thousandsSep' => ','
							);
				break;	
		}
		
	}
	
	/**
	 * <pre>&nbsp;
	 * Retorna o um valor dentro de um arrau, caso o valore seja outro array, o mesmo sera retornado.
	 * </pre>
	 * @access private
	 * @param array $array <p>Array para a busca</p>
	 * @param strin $param <p>Parametro a ser buscado</p>
	 * @return string|array
	 * @example exemplos/returnParamArray.php Arquivo de Exemplo
	 */
	private function returnParamArray($array,$param){
		
		$retorno = '';
		
		foreach ($array as $ind => $val){
			
			if($ind == $param){
				if(!is_array($array[$ind]))
					$retorno = $array[$ind].'';
				else
					$retorno = $array[$ind];
			}else{
				if(is_array($array[$ind])){
					$this->returnParamArray($array[$ind],$param);
				}
			}
		}
		
		return $retorno;
	}
	
	/**
	 * <pre>&nbsp;
	 * Percorre um array e caso seja passado outro valor para a msm propriedade a função troca pelo novo valor.
	 * 
	 * </pre>
	 * @access private
	 * @param array $default Array com todos os valores default
	 * @param array $new Array com os novos valores
	 * @return array
	 * @example exemplos/mergeOptions.php Arquivo de Exemplo
	 */
	private function mergeOptions($default,$new){

		foreach ($default as $ind => $val){
			
			if(!is_array($default[$ind])){
				if(isset($new[$ind]))
					$default[$ind] = $new[$ind];
			}else{
				if(isset($new[$ind]))
					$default[$ind] = $this->mergeOptions($default[$ind],$new[$ind]);
			}
			
		}
		
		return $default;
	}
	
	/**
	 * <pre>&nbsp;
	 * Reseta todas as opcoes utilizadas anteriormente para novos graficos.
	 * </pre>
	 */
	public function resetDefault(){
		
		/**
		 * Setando os valores default para as propriedades
		 * 
		 */
		unset($this->chart);$this->chart = $this->getDefault('chart');
		unset($this->colors);$this->colors = $this->getDefault('colors');
		unset($this->credits);$this->credits = $this->getDefault('credits');
		unset($this->labels);$this->labels = $this->getDefault('labels');
		unset($this->lang);$this->lang = $this->getDefault('lang');
		unset($this->legend);$this->legend = $this->getDefault('legend');
		unset($this->plotOptions);$this->plotOptions = $this->getDefault('plotOptions');
		unset($this->subtitle);$this->subtitle = $this->getDefault('subtitle');
		unset($this->yAxis);$this->yAxis = $this->getDefault('yAxis');
		unset($this->symbols);$this->symbols = $this->getDefault('symbols');
		unset($this->title);$this->title = $this->getDefault('title');
		unset($this->toolbar);$this->toolbar = $this->getDefault('toolbar');
		unset($this->tooltip);$this->tooltip = $this->getDefault('tooltip');
		
	}
}