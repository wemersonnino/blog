<?php /* start AceIDE restore code */
if ( $_POST["restorewpnonce"] === "9433840641e6ce07f8bdcff4771a0c078bc01486a1" ) {
if ( file_put_contents ( "/var/www/site/blog/wp-content/themes/meutudoblog/partials/blocos/perguntas-frequentes.php" ,  preg_replace( "#<\?php /\* start AceIDE restore code(.*)end AceIDE restore code \* \?>/#s", "", file_get_contents( "/var/www/site/blog/wp-content/plugins/aceide/backups/themes/meutudoblog/partials/blocos/perguntas-frequentes_2021-10-18-03-03-34.php" ) ) ) ) {
	echo __( "Your file has been restored, overwritting the recently edited file! \n\n The active editor still contains the broken or unwanted code. If you no longer need that content then close the tab and start fresh with the restored file." );
}
} else {
echo "-1";
}
die();
/* end AceIDE restore code */ ?>