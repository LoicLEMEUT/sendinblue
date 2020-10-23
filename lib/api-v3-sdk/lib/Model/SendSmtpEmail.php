<?php
/**
 * SendSmtpEmail
 *
 * PHP version 5
 *
 * @category Class
 * @package  SendinBlue\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * SendinBlue API
 *
 * SendinBlue provide a RESTFul API that can be used with any languages. With this API, you will be able to :   - Manage your campaigns and get the statistics   - Manage your contacts   - Send transactional Emails and SMS   - and much more...  You can download our wrappers at https://github.com/orgs/sendinblue  **Possible responses**   | Code | Message |   | :-------------: | ------------- |   | 200  | OK. Successful Request  |   | 201  | OK. Successful Creation |   | 202  | OK. Request accepted |   | 204  | OK. Successful Update/Deletion  |   | 400  | Error. Bad Request  |   | 401  | Error. Authentication Needed  |   | 402  | Error. Not enough credit, plan upgrade needed  |   | 403  | Error. Permission denied  |   | 404  | Error. Object does not exist |   | 405  | Error. Method not allowed  |   | 406  | Error. Not Acceptable  |
 *
 * OpenAPI spec version: 3.0.0
 * Contact: contact@sendinblue.com
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 2.4.12
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace SendinBlue\Client\Model;

use \ArrayAccess;
use \SendinBlue\Client\ObjectSerializer;

/**
 * SendSmtpEmail Class Doc Comment
 *
 * @category Class
 * @package  SendinBlue\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class SendSmtpEmail implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'sendSmtpEmail';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'sender' => '\SendinBlue\Client\Model\SendSmtpEmailSender',
        'to' => '\SendinBlue\Client\Model\SendSmtpEmailTo[]',
        'bcc' => '\SendinBlue\Client\Model\SendSmtpEmailBcc[]',
        'cc' => '\SendinBlue\Client\Model\SendSmtpEmailCc[]',
        'htmlContent' => 'string',
        'textContent' => 'string',
        'subject' => 'string',
        'replyTo' => '\SendinBlue\Client\Model\SendSmtpEmailReplyTo',
        'attachment' => '\SendinBlue\Client\Model\SendSmtpEmailAttachment[]',
        'headers' => 'object',
        'templateId' => 'int',
        'params' => 'object',
        'tags' => 'string[]'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'sender' => null,
        'to' => null,
        'bcc' => null,
        'cc' => null,
        'htmlContent' => null,
        'textContent' => null,
        'subject' => null,
        'replyTo' => null,
        'attachment' => null,
        'headers' => null,
        'templateId' => 'int64',
        'params' => null,
        'tags' => null
    ];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerFormats()
    {
        return self::$swaggerFormats;
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'sender' => 'sender',
        'to' => 'to',
        'bcc' => 'bcc',
        'cc' => 'cc',
        'htmlContent' => 'htmlContent',
        'textContent' => 'textContent',
        'subject' => 'subject',
        'replyTo' => 'replyTo',
        'attachment' => 'attachment',
        'headers' => 'headers',
        'templateId' => 'templateId',
        'params' => 'params',
        'tags' => 'tags'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'sender' => 'setSender',
        'to' => 'setTo',
        'bcc' => 'setBcc',
        'cc' => 'setCc',
        'htmlContent' => 'setHtmlContent',
        'textContent' => 'setTextContent',
        'subject' => 'setSubject',
        'replyTo' => 'setReplyTo',
        'attachment' => 'setAttachment',
        'headers' => 'setHeaders',
        'templateId' => 'setTemplateId',
        'params' => 'setParams',
        'tags' => 'setTags'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'sender' => 'getSender',
        'to' => 'getTo',
        'bcc' => 'getBcc',
        'cc' => 'getCc',
        'htmlContent' => 'getHtmlContent',
        'textContent' => 'getTextContent',
        'subject' => 'getSubject',
        'replyTo' => 'getReplyTo',
        'attachment' => 'getAttachment',
        'headers' => 'getHeaders',
        'templateId' => 'getTemplateId',
        'params' => 'getParams',
        'tags' => 'getTags'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$swaggerModelName;
    }





    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['sender'] = isset($data['sender']) ? $data['sender'] : null;
        $this->container['to'] = isset($data['to']) ? $data['to'] : null;
        $this->container['bcc'] = isset($data['bcc']) ? $data['bcc'] : null;
        $this->container['cc'] = isset($data['cc']) ? $data['cc'] : null;
        $this->container['htmlContent'] = isset($data['htmlContent']) ? $data['htmlContent'] : null;
        $this->container['textContent'] = isset($data['textContent']) ? $data['textContent'] : null;
        $this->container['subject'] = isset($data['subject']) ? $data['subject'] : null;
        $this->container['replyTo'] = isset($data['replyTo']) ? $data['replyTo'] : null;
        $this->container['attachment'] = isset($data['attachment']) ? $data['attachment'] : null;
        $this->container['headers'] = isset($data['headers']) ? $data['headers'] : null;
        $this->container['templateId'] = isset($data['templateId']) ? $data['templateId'] : null;
        $this->container['params'] = isset($data['params']) ? $data['params'] : null;
        $this->container['tags'] = isset($data['tags']) ? $data['tags'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if ($this->container['to'] === null) {
            $invalidProperties[] = "'to' can't be null";
        }
        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets sender
     *
     * @return \SendinBlue\Client\Model\SendSmtpEmailSender
     */
    public function getSender()
    {
        return $this->container['sender'];
    }

    /**
     * Sets sender
     *
     * @param \SendinBlue\Client\Model\SendSmtpEmailSender $sender sender
     *
     * @return $this
     */
    public function setSender($sender)
    {
        $this->container['sender'] = $sender;

        return $this;
    }

    /**
     * Gets to
     *
     * @return \SendinBlue\Client\Model\SendSmtpEmailTo[]
     */
    public function getTo()
    {
        return $this->container['to'];
    }

    /**
     * Sets to
     *
     * @param \SendinBlue\Client\Model\SendSmtpEmailTo[] $to List of email addresses and names (optional) of the recipients. For example, [{\"name\":\"Jimmy\", \"email\":\"jimmy98@example.com\"}, {\"name\":\"Joe\", \"email\":\"joe@example.com\"}]
     *
     * @return $this
     */
    public function setTo($to)
    {
        $this->container['to'] = $to;

        return $this;
    }

    /**
     * Gets bcc
     *
     * @return \SendinBlue\Client\Model\SendSmtpEmailBcc[]
     */
    public function getBcc()
    {
        return $this->container['bcc'];
    }

    /**
     * Sets bcc
     *
     * @param \SendinBlue\Client\Model\SendSmtpEmailBcc[] $bcc List of email addresses and names (optional) of the recipients in bcc
     *
     * @return $this
     */
    public function setBcc($bcc)
    {
        $this->container['bcc'] = $bcc;

        return $this;
    }

    /**
     * Gets cc
     *
     * @return \SendinBlue\Client\Model\SendSmtpEmailCc[]
     */
    public function getCc()
    {
        return $this->container['cc'];
    }

    /**
     * Sets cc
     *
     * @param \SendinBlue\Client\Model\SendSmtpEmailCc[] $cc List of email addresses and names (optional) of the recipients in cc
     *
     * @return $this
     */
    public function setCc($cc)
    {
        $this->container['cc'] = $cc;

        return $this;
    }

    /**
     * Gets htmlContent
     *
     * @return string
     */
    public function getHtmlContent()
    {
        return $this->container['htmlContent'];
    }

    /**
     * Sets htmlContent
     *
     * @param string $htmlContent HTML body of the message ( Mandatory if 'templateId' is not passed, ignored if 'templateId' is passed )
     *
     * @return $this
     */
    public function setHtmlContent($htmlContent)
    {
        $this->container['htmlContent'] = $htmlContent;

        return $this;
    }

    /**
     * Gets textContent
     *
     * @return string
     */
    public function getTextContent()
    {
        return $this->container['textContent'];
    }

    /**
     * Sets textContent
     *
     * @param string $textContent Plain Text body of the message ( Ignored if 'templateId' is passed )
     *
     * @return $this
     */
    public function setTextContent($textContent)
    {
        $this->container['textContent'] = $textContent;

        return $this;
    }

    /**
     * Gets subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->container['subject'];
    }

    /**
     * Sets subject
     *
     * @param string $subject Subject of the message. Mandatory if 'templateId' is not passed
     *
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->container['subject'] = $subject;

        return $this;
    }

    /**
     * Gets replyTo
     *
     * @return \SendinBlue\Client\Model\SendSmtpEmailReplyTo
     */
    public function getReplyTo()
    {
        return $this->container['replyTo'];
    }

    /**
     * Sets replyTo
     *
     * @param \SendinBlue\Client\Model\SendSmtpEmailReplyTo $replyTo replyTo
     *
     * @return $this
     */
    public function setReplyTo($replyTo)
    {
        $this->container['replyTo'] = $replyTo;

        return $this;
    }

    /**
     * Gets attachment
     *
     * @return \SendinBlue\Client\Model\SendSmtpEmailAttachment[]
     */
    public function getAttachment()
    {
        return $this->container['attachment'];
    }

    /**
     * Sets attachment
     *
     * @param \SendinBlue\Client\Model\SendSmtpEmailAttachment[] $attachment Pass the absolute URL (no local file) or the base64 content of the attachment along with the attachment name (Mandatory if attachment content is passed). For example, `[{\"url\":\"https://attachment.domain.com/myAttachmentFromUrl.jpg\", \"name\":\"myAttachmentFromUrl.jpg\"}, {\"content\":\"base64 example content\", \"name\":\"myAttachmentFromBase64.jpg\"}]`. Allowed extensions for attachment file: xlsx, xls, ods, docx, docm, doc, csv, pdf, txt, gif, jpg, jpeg, png, tif, tiff, rtf, bmp, cgm, css, shtml, html, htm, zip, xml, ppt, pptx, tar, ez, ics, mobi, msg, pub, eps, odt, mp3, m4a, m4v, wma, ogg, flac, wav, aif, aifc, aiff, mp4, mov, avi, mkv, mpeg, mpg and wmv ( If 'templateId' is passed and is in New Template Language format then both attachment url and content are accepted. If template is in Old template Language format, then 'attachment' is ignored )
     *
     * @return $this
     */
    public function setAttachment($attachment)
    {
        $this->container['attachment'] = $attachment;

        return $this;
    }

    /**
     * Gets headers
     *
     * @return object
     */
    public function getHeaders()
    {
        return $this->container['headers'];
    }

    /**
     * Sets headers
     *
     * @param object $headers Pass the set of custom headers (not the standard headers) that shall be sent along the mail headers in the original email. 'sender.ip' header can be set (only for dedicated ip users) to mention the IP to be used for sending transactional emails. Headers are allowed in `This-Case-Only` (i.e. words separated by hyphen with first letter of each word in capital letter), they will be converted to such case styling if not in this format in the request payload. For example, `{\"sender.ip\":\"1.2.3.4\", \"X-Mailin-custom\":\"some_custom_header\"}`.
     *
     * @return $this
     */
    public function setHeaders($headers)
    {
        $this->container['headers'] = $headers;

        return $this;
    }

    /**
     * Gets templateId
     *
     * @return int
     */
    public function getTemplateId()
    {
        return $this->container['templateId'];
    }

    /**
     * Sets templateId
     *
     * @param int $templateId Id of the template
     *
     * @return $this
     */
    public function setTemplateId($templateId)
    {
        $this->container['templateId'] = $templateId;

        return $this;
    }

    /**
     * Gets params
     *
     * @return object
     */
    public function getParams()
    {
        return $this->container['params'];
    }

    /**
     * Sets params
     *
     * @param object $params Pass the set of attributes to customize the template. For example, {\"FNAME\":\"Joe\", \"LNAME\":\"Doe\"}. It's considered only if template is in New Template Language format.
     *
     * @return $this
     */
    public function setParams($params)
    {
        $this->container['params'] = $params;

        return $this;
    }

    /**
     * Gets tags
     *
     * @return string[]
     */
    public function getTags()
    {
        return $this->container['tags'];
    }

    /**
     * Sets tags
     *
     * @param string[] $tags Tag your emails to find them more easily
     *
     * @return $this
     */
    public function setTags($tags)
    {
        $this->container['tags'] = $tags;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     *
     * @param integer $offset Offset
     * @param mixed   $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(
                ObjectSerializer::sanitizeForSerialization($this),
                JSON_PRETTY_PRINT
            );
        }

        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}


