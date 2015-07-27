<?php
/**
 * EmailObfuscator Widget Class file
 *
 * @author    Chris Yates
 * @copyright Copyright &copy; 2015 BeastBytes - All Rights Reserved
 * @license   BSD 3-Clause
 * @package   EmailObfuscator
 */

namespace beastbytes\emailobfuscator;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * EmailObfuscator Widget Class.
 * Obfucates emails to help prevent harvesting by spam-bots. The email address is
 * reconstructed if JavaScript is enabled
 *
 * The email address can be replaced with an obfuscated address or text informing
 * users that the email addrress is hidden
 */
class EmailObfuscator extends \yii\base\Widget
{
    /**
     * @var string The email address to obfuscate
     */
    public $address;
    /**
     * @var string rendered after the obfuscated address or message
     * @see $obfucators
     */
    public $after = '';
    /**
     * @var string rendered before the obfuscated address or message
     * @see $obfucators
     */
    public $before = '';
    /**
     * @var array Options for the email address. Valid options are:
     * subject: the subject line of the email
     * title: the title attribute of the email address tag
     * link: if true (default) a mailto link is rendered, else the address is
     * rendered in the given tag - subject and title are ignored in this case
     */
    public $emailOptions = [];
    /**
     * @var string The message that tells users the address is obfuscated
     * If {@link obfuscators} is an array this is rendered as the title of the
     * enclosing tag, else it is rendered as the content of the enclosing tag.
     */
    public $message = 'This e-mail address is protected to prevent harvesting by spam-bots';
    /**
     * @var array Obfuscators for the email address.
     * The first element replaces '.', the second replaces '@'.
     * Example [' dot ', ' at '] would display an {@link $address} of
     * "my.address@example.com" as "my dot address at example dot com"
     *
     * If empty {@link message} will be rendered in the enclosing tag instead
     * of the obfuscated email address
     */
    public $obfuscators = [];
    /**
     * @var array HTML options for the enclosing tag.
     * The following special option is recognised:
     *
     * - tag - string, optional, default = 'span' - the enclosing tag
     */
    public $options = [];

    /**
     * Initialises the widget.
     * This method publishes the JavaScript used to write the email address.
     */
    public function init()
    {
        $link = (isset($this->emailOptions['link'])
            ? $this->emailOptions['link'] : true
        );

        if (isset($this->options['id'])) {
            $this->setId($this->options['id']);
        } else {
            $this->options['id'] = $this->getId();
        }
        $id = $this->getId();

        $email  = explode('@', $this->address);
        $name   = explode('.', $email[0]);
        $domain = explode('.', $email[1]);

        foreach ($name as &$value) {
          $value = "'$value'";
        }
        foreach ($domain as &$value) {
          $value = "'$value'";
        }

        $name   = join(', ', $name);
        $domain = join(', ', $domain);

        $this->getView()->registerJs("var n_$id=new Array(".$name.");var d_$id=new Array(".$domain.");var s_$id=".(isset($this->emailOptions['subject']) ? "'?subject=".addcslashes($this->emailOptions['subject'], "'")."'" : "''").";var c_$id=document.getElementById('$id');".($link ? "var r_$id=document.createElement('a');r_$id.id='$id';r_$id.href='mailto:'+n_$id.join('.')+'@'+d_$id.join('.')+s_$id;".(isset($this->emailOptions['title']) ? "r_$id.title='".$this->emailOptions['title']."';" : "")."r_$id.appendChild(document.createTextNode(n_$id.join('.')+'@'+d_$id.join('.')));c_$id.parentNode.replaceChild(r_$id, c_$id);" : "c_$id.innerHtml=n_$id.join('.')+'@'+d_$id.join('.');"), View::POS_END);
    }

    /**
     * Runs the widget.
     * Echoes the obfuscated address
     */
    public function run()
    {
        $tag = ArrayHelper::remove($this->options, 'tag', 'span');

        echo (empty($this->obfuscators)
            ? Html::tag(
                $tag,
                $this->before . $this->message . $this->after,
                $this->options
            )
            : Html::tag(
                $ttag,
                $this->before . str_replace(
                    ['.', '@'],
                    $this->obfuscators,
                    $this->address
                ) . $this->after,
                array_merge(['title' => $this->message], $this->options)
            )
        );
    }
}