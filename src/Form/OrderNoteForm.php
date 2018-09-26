<?php

namespace Drupal\commerce_note\Form;

use Drupal\commerce_order\Entity\Order;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class OrderNoteForm.
 */
class OrderNoteForm extends FormBase {

  private $order;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'commerce_note_order_note_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['order_number'] = [
      '#type' => 'markup',
      '#markup' => '订单号：'.$this->getOrder()->getOrderNumber()
    ];

    $form['order_note'] = [
      '#type' => 'textarea',
      '#title' => $this->t('管理员订单备注'),
      '#description' => $this->t('管理员订单备注是用于后台管理员对订单的特别事项进行注明。'),
      '#default_value' => $this->getOrder()->get('order_note')->value,
      '#weight' => '0',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $this->getOrder()
      ->set('order_note', $values['order_note'])
      ->save();

    \Drupal::messenger()->addMessage('备注信息已成功保存！');
  }


  /**
   * @return Order
   * @throws \Exception
   */
  private function getOrder() {
    if ($this->order instanceof Order) return $this->order;
    else {
      $order_id = $this->getRouteMatch()->getParameter('commerce_order');
      $order = Order::load($order_id);
      if ($order instanceof Order) {
        $this->order = $order;
        return $this->order;
      } else {
        throw new \Exception('找不到订单['.$order_id.']');
      }
    }
  }
}
