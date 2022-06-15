
<div class="carrinho">

<?php
	
	if(@$_SESSION["carrinho"] == false):
		echo '<br /><div class="alert alert-warning text-center" role="alert">Seu carrinho está vázio.</div><br />';
	else:
		unset($_SESSION["typeDelivery"]);
		unset($_SESSION["addressDelivery"]); ?>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Produto</th>
					<th>Descrição</th>
					<th class="text-center">Preço unitário</th>
					<th class="text-center">Quantidade</th>
					<th class="text-center">Total</th>
				</tr>
			</thead>
			<tbody><?php 
				$totalValores     = 0;
				$totalProdutos    = 0;
				$totalPeso        = 0;
				$totalComprimento = 0;
				$totalLargura     = 0;
				$totalAltura      = 0;
				$totalDimensoes   = 0;
				
				//unset($_SESSION['carrinho']);
				
				foreach ($_SESSION['carrinho'] as $ID => $carrinho): 
					// $totalValores     += $carrinho["PRECO"]*$carrinho["QNT"];
					// $totalProdutos    += $carrinho["QNT"];
					// $totalPeso        += $carrinho["PESO"]*$carrinho["QNT"];
					// $totalComprimento += $carrinho["COMPRIMENTO"];
					// $totalLargura     += $carrinho["LARGURA"];
					// $totalAltura      += $carrinho["ALTURA"];
					// $dimensoes        = $totalComprimento*$totalLargura*$totalAltura*$carrinho["QNT"];
					$totalValores     += $carrinho["PRECO"]*$carrinho["QNT"];
					$totalProdutos    += $carrinho["QNT"];
					$totalPeso        += $carrinho["PESO"]*$carrinho["QNT"];
					$totalDimensoes   += $carrinho["COMPRIMENTO"]*$carrinho["LARGURA"]*$carrinho["ALTURA"];
					$dimensoes         = $totalDimensoes*$carrinho["QNT"];
					$buscaCaixa = @$conn->query("SELECT * FROM caixas WHERE total > '$dimensoes' OR total ='$dimensoes' ORDER BY total ASC LIMIT 1")->fetch(PDO::FETCH_OBJ);
					$caixa = $buscaCaixa == false ? @$conn->query("SELECT * FROM caixas ORDER BY total DESC LIMIT 1")->fetch(PDO::FETCH_OBJ) : $buscaCaixa; ?>
					<tr id="tr<?php echo $ID; ?>">
						<td class="imagem-carrinho">
							<img src="uploads/produtos/<?php echo $carrinho["FOTO"]; ?>" alt="<?php echo $carrinho["NOME"]; ?>" style="max-width: 80px; max-height: 50px;" />
						</td>
						<td class="descricao-produto">
							<a href="detalhes/<?php echo tira_acentos($carrinho["NOME"]); ?>/<?php echo $carrinho["ID"]; ?>"><?php echo $carrinho["NOME"]; ?></a>
							<?php echo ($carrinho["OPCIONAL_NOME"]) ? '<br><small>'.$carrinho["OPCIONAL_NOME"].'</small>' : ''; ?>
						</td>
						<td class="text-center valor-item">
							<h1>R$ <?php echo number_format($carrinho["PRECO"], 2, ',', '.'); ?></h1>
						</td>
						<td class="text-center quantidade-item">
							<form name="formAltQnt" class="formAltQnt" method="post" action="#">
								<input name="qnt[<?php echo $ID; ?>]" id="qnt<?php echo $ID; ?>" class="form-control input-qnt" type="number" value="<?php echo $carrinho["QNT"]; ?>" min="1" />
								<div class="result"></div>
							</form>
			 
							<a href="javascript:;" data-produto-id="<?php echo $ID; ?>" class="btn btn-xs remover-carrinho"><span class="glyphicon glyphicon-trash"></span> Remover</a>
							<div class="result-remover"></div>
						</td>
						<td class="text-center valor-total-item">
							<h1>R$  <span id="valorTotalProduto<?php echo $ID; ?>"><?php echo number_format($carrinho["PRECO"]*$carrinho["QNT"], 2, ',', '.'); ?></span></h1>
							<p hidden><?php echo $ID; ?></p>
						</td>
					</tr>
					<?php 
					// endif;
				endforeach; ?>
			</tbody>
		</table>

		<?php 
		// echo 'Dim.: '.$dimensoes."<br />";
		// echo 'Peso total: '.$totalPeso."<br />";
		// echo 'Caixa: '.$caixa->ID."<br />";
		?>
		<div class="carrinho calcular-frete col-md-6">
			<form name="formFreteProd" class="formFreteProd" action="#" method="post">
				<input name="nosp_frete" type="hidden" value="" />
				<input id="pesoTotal" name="pesoTotal" type="hidden" value="<?php echo encode($totalPeso); ?>" />
				<input id="caixaAltura" name="caixaAltura" type="hidden" value="<?php echo encode($caixa->altura); ?>" />
				<input id="caixaLargura" name="caixaLargura" type="hidden" value="<?php echo encode($caixa->largura); ?>" />
				<input id="caixaComprimento" name="caixaComprimento" type="hidden" value="<?php echo encode($caixa->comprimento); ?>" />
				<input class="form-control" type="text" name="cep" id="cepProd" placeholder="Qual o CEP para o frete" />
				<input type="submit" class="btn btn-default pull-left" value="OK">
				<div class="result"></div>
			</form>
		</div>


		<div class="carrinho total col-md-6  text-right font-weight-bold" id="valorTotal">
			VALOR TOTAL <h1 id="valorTotal">R$ <?php echo number_format($totalValores, 2, ',', '.'); ?></h1><?php 
			if($totalValores >= 50.00){
				$juros = (1.4/100)*$totalValores;
				$valorParcela = $totalValores/12+$juros;
			}else{
				$valorParcela = false;
			}
			
			if(@$valorParcela == true): ?>
				<span>Ou em até 12x de <strong>R$ <?php echo number_format($valorParcela, 2, ',', '.'); ?></strong></span>
			<?php endif; 
			?>
			
		</div>

		<div class="clear"></div>
		<script>
		$(document).ready(function(){
			$(".btn-cupom").click(function(){
				$.ajax({
					url: "",
					type: "POST",
					data: {},
					success: function(retorno){
						console.log(retorno)
					}
				})
			})
			
			/*
			seletocard_user();
			function seletocard_user(){
				$.ajax({ 
					type: "POST",
					url: "http://seletocard2.localhost/api/rest/check_user/",
					dataType: 'json',
					//xhrFields: { withCredentials: true }},
					data: {
						cpf: '02701732930',
					},
					success: function(json){
						console.log(json)
						
						//$('.seletocard .credito-atual').html("R$ "+json.body.credito_restante);
						//if(json.result){
						//	swal({
						//		title: "Sucesso", 
						//		text: json.message, 
						//		type: "success"},
						//		function(){ 
						//		   location.reload();
						//		}
						//	)
						//} else if (response == false){
						//	swal("Erro", json.message, "warning")
						//}
						
					},
					error: function(error){
						console.log(error);
					},
				});
			}*/
			
		})
		</script>
		<a href="home" class="btn pull-left btn-big" style="background-color: #EEE !important;"><span class="glyphicon glyphicon-chevron-left"></span> CONTINUAR COMPRANDO</a>
		<a href="checkout" class="btn pull-right btn-big finalizar">FINALIZAR COMPRA</a>
		<?php 
	endif;
	?>

</div>

<div class="clear"></div>

<br />