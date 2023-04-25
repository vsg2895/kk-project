<?php namespace Jakten\Mail;

/**
 * Class TestMail
 * @package Jakten\Mail
 */
class TestMail extends AbstractMail
{
    /**
     * @var string $email
     */
    public $email;

    /**
     * TestMail constructor.
     * @param String $email
     */
    public function __construct(String $email)
    {
        $this->setQueue();
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('email::test_mail')
            ->to($this->email)->subject('Test av epostutskick');
    }
}
