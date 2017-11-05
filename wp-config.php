<?php
/**
 * As configurações básicas do WordPress
 *
 * O script de criação wp-config.php usa esse arquivo durante a instalação.
 * Você não precisa usar o site, você pode copiar este arquivo
 * para "wp-config.php" e preencher os valores.
 *
 * Este arquivo contém as seguintes configurações:
 *
 * * Configurações do MySQL
 * * Chaves secretas
 * * Prefixo do banco de dados
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/pt-br:Editando_wp-config.php
 *
 * @package WordPress
 */
define('FS_METHOD', 'direct');

// ** Configurações do MySQL - Você pode pegar estas informações
// com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define('DB_NAME', 'fastDelivery');

/** Usuário do banco de dados MySQL */
define('DB_USER', 'root');

/** Senha do banco de dados MySQL */
define('DB_PASSWORD', 'root');

/** Nome do host do MySQL */
define('DB_HOST', 'localhost');

/** Charset do banco de dados a ser usado na criação das tabelas. */
define('DB_CHARSET', 'utf8');

/** O tipo de Collate do banco de dados. Não altere isso se tiver dúvidas. */
define('DB_COLLATE', '');

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las
 * usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org
 * secret-key service}
 * Você pode alterá-las a qualquer momento para invalidar quaisquer
 * cookies existentes. Isto irá forçar todos os
 * usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'p+0v>>RMlQ*TqKl{];EZ=Y^8|JHs0.|E+j4;*R[L9{]N|Ev+noML$jRj@WhT/c1E');
define('SECURE_AUTH_KEY',  '2S+cK2s^|fCqzs-O^v|D.M,rApp1vqjpTL,b-(|rSL-4+{#HUw[P~,K>[mR?gp@N');
define('LOGGED_IN_KEY',    'wE;8?,ZZZna9-`j~~gp*+.L+`Dr{`8s+;9*]t?k*iUFS3q-oTPPjL_qj?o({Vf!L');
define('NONCE_KEY',        '4;}%sonvt@lmhO- u@)[.5!nd.hVJUT9C{w:+[>D>)%=z^}T@>aCJFh4FP=!j#}K');
define('AUTH_SALT',        '3M[khn.W7jq-nB*2 io{BqEaF0Jg!Q[Lz`n#ldcpNQ#e|8f*n @ASBf#`w%X:> j');
define('SECURE_AUTH_SALT', 'F0z|CYC7IBQ8U]+EhKUG-Rq(>Eb*Yj=N4w]D7E1;>k+c$m5Y=j0AvvV:HgC4N(D2');
define('LOGGED_IN_SALT',   'h0mK8n!r7QJfKz|x*q??wk*t}{q[m@P[&n4:eG548]H(4aM6q]Q++r3qf-P|7n;Y');
define('NONCE_SALT',       'oM{2M`ns~ial8Jb%MJB/kxRrIwr =vy<p 2lYWUlQJ&3a-VN;|ISx-bQDU YD.i[');

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der
 * um prefixo único para cada um. Somente números, letras e sublinhados!
 */
$table_prefix  = 'wp_';

/**
 * Para desenvolvedores: Modo de debug do WordPress.
 *
 * Altere isto para true para ativar a exibição de avisos
 * durante o desenvolvimento. É altamente recomendável que os
 * desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 *
 * Para informações sobre outras constantes que podem ser utilizadas
 * para depuração, visite o Codex.
 *
 * @link https://codex.wordpress.org/pt-br:Depura%C3%A7%C3%A3o_no_WordPress
 */
define('WP_DEBUG', false);

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Configura as variáveis e arquivos do WordPress. */
require_once(ABSPATH . 'wp-settings.php');
