<?php

namespace Drupal\sendinblue\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the Contact entity.
 *
 * @ContentEntityType(
 *   id = "sendinblue_signup_form",
 *   label = @Translation("Sendinblue Signup Form entity"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\sendinblue\Entity\Controller\SignupListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\sendinblue\Form\SignupForm",
 *       "edit" = "Drupal\sendinblue\Form\SignupForm",
 *       "delete" = "Drupal\sendinblue\Form\SignupDeleteForm",
 *     },
 *     "access" = "Drupal\sendinblue\Access\SignupAccessControlHandler",
 *   },
 *   base_table = "sendinblue_signup",
 *   admin_permission = "administer sendinblue",
 *   fieldable = TRUE,
 *   entity_keys = {
 *     "id" = "mcsId",
 *     "name" = "name",
 *   },
 *   links = {
 *     "canonical" = "/sendinblue_signup_form/{sendinblue_signup_form}",
 *     "add-form" = "/sendinblue_signup_form/add",
 *     "edit-form" = "/sendinblue_signup_form/{sendinblue_signup_form}/edit",
 *     "delete-form" = "/sendinblue_signup_form/{sendinblue_signup_form}/delete",
 *     "collection" = "/sendinblue_signup_form/list"
 *   },
 * )
 */
class Signup extends ContentEntityBase {

  /**
   * {@inheritdoc}
   *
   * Define the field properties here.
   *
   * Field name, type and size determine the table structure.
   *
   * In addition, we can define how the field and its content can be manipulated
   * in the GUI. The behaviour of the widgets used can be determined here.
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    // Standard field, used as unique if primary index.
    $fields['mcsId'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Contact entity.'))
      ->setReadOnly(TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Contact entity.'))
      ->setSettings([
        'default_value' => '',
        'max_length' => 32,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['mcLists'] = BaseFieldDefinition::create('map')
      ->setLabel(t('settings'))
      ->setDescription(t('The ID of the Contact entity.'));

    $fields['mode'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('mode'))
      ->setDescription(t('The ID of the Contact entity.'));

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('The name of the Contact entity.'))
      ->setSettings([
        'default_value' => '',
        'max_length' => 32,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['settings'] = BaseFieldDefinition::create('map')
      ->setLabel(t('settings'))
      ->setDescription(t('The ID of the Contact entity.'));

    $fields['status'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('status'))
      ->setDescription(t('The ID of the Contact entity.'))
      ->setDefaultValue(1);

    $fields['module'] = BaseFieldDefinition::create('string')
      ->setLabel(t('module'))
      ->setDescription(t('The name of the Contact entity.'));

    return $fields;
  }

}
