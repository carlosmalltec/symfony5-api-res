<?php

namespace App\Components;

use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

/**
 * Description of Email
 * <b>No final da classe tem um exemplo. actionEmail</b>
 * @author Carlos Santos
 */
class HelperEmail
{

    private $Host;
    private $Port;
    private $Security;
    private $User;
    private $Pass;
    private $EnvioAnexo; //True habilita envio de anexo, false desabilita

    /** RESULTSET */
    private $Result;
    private $Error;

    /** Objeto */
    private $Mailer;

    /**
     * Pode criar um novo objeto ou usar config default. <b>No final da classe tem um exemplo. actionEmail</b>
     * @param string $host - Host smtp.domino.com.br | mail.dominio.com.br | outro
     * @param string $port - Porta do smtp. Exemplo: 587 | 25 | 465 outras
     * @param string $security - O modo de criptografia a ser usado ao usar smtp. Os valores válidos são tls, ssl, ou null.
     * @param string $pass - Senha do SMTP
     * @param string $user - E-mail do SMTP
     * @param string $envioAnexo True para habilitar envio de anexo e default false
     * @return object Retorna instância do objeto
     */
    function __construct($host = null, $port = null, $security = null, $pass = null, $user = null, $envioAnexo = null)
    {
        // Create the Transport
        $transport = (new Swift_SmtpTransport(
            $this->Host = isset($host) ? $host : 'mail.malltec.com.br', 
            $this->Port = isset($port) ? $port : '587'
            ))
            ->setUsername($this->User = isset($user) ? $user : 'oi@malltec.com.br')
            ->setPassword($this->Pass = isset($pass) ? $pass : '0f2c26z+iF2I');

        // Create the Mailer using your created Transport
        $this->Mailer = new Swift_Mailer($transport);
    }

    /**
     * <b>Envio de E-mails :</b>
     * <b>No final da classe tem um exemplo. actionEmail</b>
     * @param array $remetente Vetor [email=>nome]
     * @param array $destinatario Vetor [email=>nome,email1=>nome1,email2=>nome2]
     * @param String $assunto Assunto do e-mail
     * @param String $body Corpo do e-mail
     */
    public function sendEmail(array $remetente, array $destinatario, String $assunto, String $body)
    {
        $message = (new Swift_Message($assunto))
            ->setFrom($remetente)
            ->setTo($destinatario)
            ->setBody($body);

        if ($this->Mailer->send($message)) {
            $this->Result = true;
            $this->Error = 'E-mail enviado com sucesso!';
        } else {
            $this->Result = false;
            $this->Error = 'E-mail não enviando!';
        }
    }

    /**
     * <b>Verificar o status do E-Mail:</b> Executando um getResult é possível verificar se o E-mail foi enviado ou não. Retorna
     * TRUE ou FALSE.
     * @return boolean  = True ou False
     */
    public function getResult()
    {
        return $this->Result;
    }

    /**
     * <b>Obter Erro:</b> Retorna um array associativo com um code, um title, um erro e um tipo.
     * @return ARRAY $Error = Array associatico com o erro
     */
    public function getError()
    {
        return $this->Error;
    }
}

/*
public function actionEmail() {
    $sendMail = new Email();
    $remetente = ['remetente@email.com.br' => 'Nome Remetente'];
    $destinatario = ['email@email.com' => 'Nome Destinatario','emails@emails.com' => 'Nome Destinatario'];
    $assunto = 'Assunto do email';
    $body = 'conteúdo do e-mail';

    $sendMail->sendEmail(array $remetente, array $destinatario, String $assunto, String $body);
    if ($sendMail->getResult()) {
        $retorno = $sendMail->getError();
    } else {
        $retorno = $sendMail->getError();
    }
    var_dump($retorno);
    die();
}
 */
