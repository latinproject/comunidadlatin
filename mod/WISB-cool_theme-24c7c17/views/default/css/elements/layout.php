<?php
/**
 * Page Layout
 *
 * Contains CSS for the page shell and page layout
 *
 * Default layout: 981px wide, centered. Used in default page shell
 *
 */
$url = elgg_get_site_url();
?>
/* AO: Abril 25, cambios para que la página inicial se redimensione sin problemas*/
html, body {
    width: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
}
/* AO: Abril 25, cambios para que la página inicial se redimensione sin problemas*/
.elgg-page {
    position: relative;
    width: 100%;
    min-width: 1100px;
    min-height: 100%;
    margin:0;
    padding:0;
}
/* AO: Abril 25, cambios para que la página inicial se redimensione sin problemas*/
.elgg-page-body {
/* AO: Mayo 2
    padding: 10px 80px; */
    padding: 30px;
}
/* <style>*/
/* ***************************************
    PAGE LAYOUT
*************************************** */
/***** DEFAULT LAYOUT ******/

/*AO: Abril 18, modificados los sgtes 2 estilos para que el banner sea link */

.elgg-page-default .elgg-page-header > .elgg-inner{
    width: 981px;
    margin: 0 auto;
    height: 128px;
}

.elgg-page-header .elgg-inner #logo_sitio{
    background: url(<?php echo $url; ?>_graphics/<?php echo elgg_echo("banner:image"); ?>) 0 0 no-repeat;
    background-size: 995px 128px;
    width: 995px;
    height: 128px;
    display: block;
}

.elgg-page-default .elgg-page-body > .elgg-inner {
    width: 981px;
    margin: 0 auto;
}

.elgg-page-footer > .elgg-inner:after {
    display: block;
    content: '.';
    clear: both;
    visibility: hidden;
    height: 0;
}
/***** TOPBAR ******/
.elgg-page-topbar {
    position: fixed;
    right: 0;
    left: 0;
    z-index: 1030;
    margin-bottom: 0;
    padding-left: 20px;
    padding-right: 20px;
    background-color: #2c2c2c;
    background-image: -moz-linear-gradient(top, #333333, #222222);
    background-image: -ms-linear-gradient(top, #333333, #222222);
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#333333), to(#222222));
    background-image: -webkit-linear-gradient(top, #333333, #222222);
    background-image: -o-linear-gradient(top, #333333, #222222);
    background-image: linear-gradient(top, #333333, #222222);
    background-repeat: repeat-x;
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#333333', endColorstr='#222222', GradientType=0);
    -webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25), inset 0 -1px 0 rgba(0, 0, 0, 0.1);
    -moz-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25), inset 0 -1px 0 rgba(0, 0, 0, 0.1);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25), inset 0 -1px 0 rgba(0, 0, 0, 0.1);
}

.elgg-page-topbar > .elgg-inner {
    padding-top: 6px;
    width: 981px;
    margin: 0 auto;
    position: relative;
}

.elgg-page-topbar ~ .elgg-page-body {
    padding-top: 38px;
}
/***** PAGE MESSAGES ******/
.elgg-system-messages {
    position: fixed;
    top: 24px;
    right: 20px;
    max-width: 500px;
    z-index: 1000;
}

.elgg-system-messages li {
    margin-top: 10px;
}

.elgg-system-messages li p {
    margin: 0;
}
/***** PAGE HEADER ******/
.elgg-page-header {
    position: relative;
    background: #fff;
}

.elgg-page-header > .elgg-inner {
    position: relative;
}
/***** PAGE BODY LAYOUT ******/
.elgg-layout {
    min-height: 360px;
}

.elgg-layout-one-column {
    padding: 10px 0;
}

.elgg-sidebar {
    margin-right: 15px;
    position: relative;
    padding: 20px 0;
    float: left;
    width: 181px;
    min-height: 360px;
}

.elgg-sidebar-alt {
    position: relative;
    float: right;
    width: 244px;
    margin-left: 20px;
    min-height: 360px;
}

.elgg-main {
    position: relative;
    min-height: 360px;
}

.elgg-layout-two-sidebar > .elgg-body,.elgg-layout-one-sidebar > .elgg-body {
    border: 1px solid #B3B3B3;
    border-top: 0;
    padding: 15px 20px;
}

.elgg-layout > .elgg-body > .elgg-head {
    padding-bottom: 3px;
    margin-bottom: 10px;
}

.elgg-sidebar-right {
    position: relative;
    padding-top: 20px;
    float: right;
/*AO: Abril 24, modificado por ajuste en idioma español*/
/*    width: 190px; */
    width: 231px;
    min-height: 360px;
}
/***** PAGE FOOTER ******/
.elgg-page-default .elgg-page-footer > .elgg-inner {
    width: 981px;
    margin: 0 auto;
}
/*.elgg-page-default .elgg-page-footer > .elgg-inner {margin-left: 181px; padding: 0; margin-right:181px}*/
/* AO: Abril 25, cambios para que la página inicial se redimensione sin problemas*/
.elgg-page-footer {
    position: relative;
    bottom: 0;
    width: 100%;
/*  height: 80px;*/
/*AO: Abril 29, página principal*/
    height: 110px;
    left: 0;
    margin:0;
/*AO: Mayo 2, página principal
    padding:0; */
    padding: 20px 0 0 0;
}

.elgg-page-footer {
    color: #fff;
    background-color: #000000;
}

.elgg-page-footer a:hover {
    color: #666;
}
/***** ESTILOS AÑADIDOS ******/
.footer_izq {
/*AO: Abril 29, página principal*/
/*    width: 200px;*/
    width: 200px;
    float: left;
}

.footer_der {
    margin-left: 0;
    width: 400px;
    float: right;
}

.footer_izq .titulo, .footer_der .titulo {
    display: block;
    letter-spacing: normal;
    line-height: 1em;
    font-size: 140%;
    color: #277fcf;
    margin-bottom: 10px;
    padding-top: 10px;
    margin: 0;
}

.footer_izq p, .footer_der p {
    margin-bottom: 3px;
}
/*Algunos cambios de estilo en página principal*/
/*AO: Mayo 13, cambiados estilos de bloque_vd por slider*/
.bloque_vd{
    float: left;
    width: 705px;
    border: 1px solid silver;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
    min-height: 390px;
    padding: 10px;
    position: relative;
    overflow: hidden;
    margin-bottom: 10px;
}

.bloque_vd > ul {
    position: relative;
    -webkit-transition: 0.5s left;
    -moz-transition: 0.5s left;
    -ms-transition: 0.5s left;
    -o-transition: 0.5s left;
    list-style: none;
    margin: 0; padding: 0;
}

.bloque_vd > ul > li {
	float: left;
	width: 705px;
	min-height: 390px;
	padding-right: 15px;
}

.bloque_vd h1 {
    color: #000000;
    font-family: Arial, sans-serif;
    font-size: 22px;
    font-weight: bold;
    line-height: 1.4em;
    text-align: center;
    padding-bottom: 15px;
}

.bloque_vd p{
    font-size: 14px;
}

.bloque_t1 {
    width: 45%;
    float: left;
    margin-top: 30px;
    border: 1px solid silver;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
    height: 280px;
    padding: 10px;
    position: relative;
}

.bloque_t1 {
    background-image: url(<?php echo $url; ?>_graphics/p_box.gif);
    background-position: 50% 100%;
    background-repeat: no-repeat no-repeat;
    margin-right: 0;
    background-size: 100%;
}

.bloque_t1 h2 {
    color: #000;
    font-weight: bold;
}

.bloque_t2 {
    width: 45%;
    float: right;
    margin-top: 30px;
    border: 1px solid silver;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
    height: 280px;
    padding: 10px;
    position: relative;
}

.bloque_t2 {
    background-image: url(<?php echo $url; ?>_graphics/p_box.gif);
    background-position: 50% 100%;
    background-repeat: no-repeat no-repeat;
    margin-right: 0;
    background-size: 100%;
}

.bloque_t2 h2 {
    color: #000;
    font-weight: bold;
}

/*AO: Abril 24, estilo del div contenido para alinear panel de búsqueda y vista de comunidades en página principal*/
.bloque_t2 .contenido{
    margin: 0 auto;
    width: 90%;
}

/*AO: Abril 24, modificado el ancho del input de búsqueda en página principal*/
.bloque_t2 .searchInput{
    width: 80%;
}

.elgg-sidebar-right .elgg-head {
    background-color: #fff;
    border-top: 0;
    border-bottom-color: #CCCCCC;
    border-bottom-style: solid;
    border-bottom-width: 1px;
    margin-bottom: 5px;
}

.elgg-sidebar-right .elgg-head h3 {
    font-size: 14px;
    color: #0054A7;
    font-weight: bold;
}
/*Login form de página principal*/

.elgg-form-login label {
    color: #333333;
    font-weight: bold;
    font-size: 13px;
}

.elgg-form-login .elgg-input-text {
    width: 100%;
}

.elgg-form-login .elgg-input-password {
    width: 100%;
}

/*AO: Abril 24, modificado por ajuste en idioma español*/

.elgg-form-login .elgg-foot{
        width: 100%;
}

.elgg-form-login .elgg-foot label{
        width: 52%;
}

.elgg-form-login .elgg-foot .elgg-button-submit{
    float: right;
}
/*AO*/

.elgg-menu-general li {
    display: inline-block;
    padding: 0;
    margin: 0;
}

.elgg-menu-general li a:hover {
    text-decoration: underline;
}
/*Para formulario de registro */
.register_form{
	float: right;
	width: 40%;
	padding: 25px;
	margin: 15px 0 0 0;
	background: rgb(247, 247, 247);
	border: 1px solid #777;
}

.register_form label{
	font-size: 13px;
}
/******************************/

.elgg-form-register #profile_manager_register_left input{padding: 5px}
.elgg-form-register #profile_manager_register_left .label_captcha{display: block; margin-top: 15px}

/*AO: Abril 18, estilos añadidos para sidebar de página de registro */


.sidebar_r{
	float: left;
	width: 53%;
	padding-top: 40px;
        
/*        background-image:url('<?php echo $url; ?>_graphics/latinAmerica.png');
        background-size: 100% 100%;
        background-repeat:no-repeat;*/
}

.benefit{
	overflow: hidden;
	margin-bottom: 25px;
}

.benefitImage{
	float: left;
	width: 80px;	
	margin-left: 10px;        
}

.benefit h2{
	color: #0054A7;
	font-weight: normal;
	font-size: 20px;
	color: #000;
        font-family: "Century Gothic", CenturyGothic, AppleGothic, sans-serif;
}

.benefit .ben_text{
	width: auto;
	margin-left: 21%;
        text-align: justify;
}

.ben_text p{
	font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
	line-height: 19px;
	font-size: 13px;
	color: #444;
}

.registerf{
	max-width: 80%;
}

hr.registerBreak {
    border: 0;
    height: 0;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
    border-bottom: 1px solid rgba(255, 255, 255, 0.3);
    margin-bottom: 30px;
}

.legal{
    width: 100%;
    clear: both;
}

.footer_der ul{
	list-style-type: none;
	margin:0;
	padding:0;
}

.footer_der ul li{ 
	display: inline;
}

.footer_der ul li a{
	padding-left: 5px;
}

.footer_der ul li::after{
	content: "|";
	padding: 0 .5em;
}

.footer_der ul li:last-child::after{
	display: none;
}

/*AO: Mayo 4, estilos añadidos */

.bloque_t1 ul{
	list-style-type: none;
	padding: 0;
	margin-top: 30px;
}

.bloque_t1 ul li{
	background-image: url(<?php echo $url; ?>_graphics/next.png);
	background-repeat: no-repeat;
	background-size: 24px;
	background-position: top left;
	margin: 15px 0;
	height: 48px;
	padding-left: 35px;
	font-weight: bold;
	font-size: 16px;
	color: #000;
}

.bloque_t1 .contenido{
    margin: 0 auto;
    width: 90%;
}


.bloque_t1 a{
	position: absolute;
	bottom: 15px;
	right: 30px;
	color: rgb(0, 61, 121);
}

.link_cr{
        color: rgb(0, 61, 121);
	font-weight: bold;
}

/*.bloque_about {
    float: left;
    margin-top: 15px;
    border: 1px solid silver;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
    min-height: 350px;
    padding: 15px;
    position: relative;
}

.bloque_about {
    background-image: url(<?php echo $url; ?>_graphics/p_box.gif);
    background-position: 50% 100%;
    background-repeat: no-repeat no-repeat;
    margin-right: 0;
    background-size: 100%;
}

.bloque_about h2 {
    color: #000;
    font-weight: bold;
}

.bloque_about div {
    font-size:12px;
}*/

.lista_info{
    display: inline;
}

/*AO: Mayo 13, estilos añadidos para opciones de slider */
.slider_opt ul{
	text-align: center;
        list-style-type: none;
        margin: 0;
        padding: 0;
}

.slider_opt ul li{
        display: inline;
	font-weight: bold;
}

.slider_opt ul li a{
        padding-left: 15px;
}

/* AO, Agosto 2 fancy popup*/
.contenido #hidden_link{
	display: none;
}
