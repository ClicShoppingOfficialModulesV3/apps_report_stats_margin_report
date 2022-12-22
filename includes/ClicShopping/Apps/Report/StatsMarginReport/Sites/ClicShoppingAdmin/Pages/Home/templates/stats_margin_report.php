<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_Currencies = Registry::get('Currencies');

  $CLICSHOPPING_StatsMarginReport = Registry::get('StatsMarginReport');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  // get list of order status
  $orders_statuses = [];
  $orders_status_array = [];

  $QordersStatus = $CLICSHOPPING_StatsMarginReport->db->prepare('select orders_status_id,
                                                                 orders_status_name
                                                          from :table_orders_status
                                                          where language_id = :language_id
                                                          order by orders_status_id
                                                         ');

  $QordersStatus->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
  $QordersStatus->execute();

  while ($orders_status = $QordersStatus->fetch()) {
    $orders_statuses[] = ['id' => $QordersStatus->valueInt('orders_status_id'),
      'text' => $QordersStatus->value('orders_status_name')
    ];
    $orders_status_array[$QordersStatus->valueInt('orders_status_id')] = $QordersStatus->value('orders_status_name');
  }

  $Qdate = $CLICSHOPPING_StatsMarginReport->db->query("SELECT curdate() as time");
  $d2 = $Qdate->fetch();


  $d['time'] = date('Y-m-d');


  if (isset($report)) {
    $report = HTML::sanitize($report);
  } else {
    $report = 'all';
  }

  switch ($report) {
    case 'all':
      $header = $CLICSHOPPING_StatsMarginReport->getDef('text_report_header');
      break;
    case 'daily':

      $Qdate = $CLICSHOPPING_StatsMarginReport->db->query("SELECT curdate() as time");
      $d = $Qdate->fetch();

      $header = $CLICSHOPPING_StatsMarginReport->getDef('text_report_headerfrom_day');
      break;
    case 'yesterday':

      $Qdate = $CLICSHOPPING_StatsMarginReport->db->query("SELECT DATE_SUB(curdate(), INTERVAL 1 DAY) as time");
      $d = $Qdate->fetch();

      $l = 1;
      $header = $CLICSHOPPING_StatsMarginReport->getDef('text_report_headerfrom_yesterday');
      break;
    case 'weekly':
    case 'lastweek':
      if ($report == "lastweek") {
// last week
        $adjust = 7;
      } else {
        $adjust = 0;
      }
      $l = 7;  // seven day window length

      $QweekdayQuery = $CLICSHOPPING_StatsMarginReport->db->query("SELECT weekday(now()) as weekday");
      $weekday = $QweekdayQuery->fetch();

      $day = 6 + ($weekday['weekday'] - 6);

//echo $day;
      switch ($day) {
        case '0':
          $Qdate = $CLICSHOPPING_StatsMarginReport->db->query("SELECT curdate() - INTERVAL " . ($adjust + 1) . " DAY as time");
          $Qdate2 = $CLICSHOPPING_StatsMarginReport->db->query("SELECT curdate() - INTERVAL " . ($adjust + 1 - 7) . " DAY as time");
          break;
        case '1':
          $Qdate = $CLICSHOPPING_StatsMarginReport->db->query("SELECT curdate() - INTERVAL " . ($adjust + 2) . " DAY as time");
          $Qdate2 = $CLICSHOPPING_StatsMarginReport->db->query("SELECT curdate() - INTERVAL " . ($adjust + 2 - 7) . " DAY as time");
          break;
        case '2':
          $Qdate = $CLICSHOPPING_StatsMarginReport->db->query("SELECT curdate() - INTERVAL " . ($adjust + 3) . " DAY as time");
          $Qdate2 = $CLICSHOPPING_StatsMarginReport->db->query("SELECT curdate() - INTERVAL " . ($adjust + 3 - 7) . " DAY as time");
          break;
        case '3':
          $Qdate = $CLICSHOPPING_StatsMarginReport->db->query("SELECT curdate() - INTERVAL " . ($adjust + 4) . " DAY as time");
          $Qdate2 = $CLICSHOPPING_StatsMarginReport->db->query("SELECT curdate() - INTERVAL " . ($adjust + 4 - 7) . " DAY as time");
          break;
        case '4':
          $Qdate = $CLICSHOPPING_StatsMarginReport->db->query("SELECT curdate() - INTERVAL " . ($adjust + 5) . " DAY as time");
          $Qdate2 = $CLICSHOPPING_StatsMarginReport->db->query("SELECT curdate() - INTERVAL " . ($adjust + 5 - 7) . " DAY as time");
          break;
        case '5':
          $Qdate = $CLICSHOPPING_StatsMarginReport->db->query("SELECT curdate() - INTERVAL " . ($adjust + 6) . " DAY as time");
          $Qdate2 = $CLICSHOPPING_StatsMarginReport->db->query("SELECT curdate() - INTERVAL " . ($adjust + 6 - 7) . " DAY as time");
          break;
        case '6':
          $Qdate = $CLICSHOPPING_StatsMarginReport->db->query("SELECT curdate() - INTERVAL " . $adjust . " DAY as time");
          $Qdate2 = $CLICSHOPPING_StatsMarginReport->db->query("SELECT curdate() - INTERVAL " . ($adjustd - 7) . " DAY as time");
          break;
      }

      $d = $Qdate->fetch();
      $d2 = $Qdate2->fetch();

      $header = $CLICSHOPPING_StatsMarginReport->getDef('text_report_headerfrom_week');
      break;
    case 'monthly':

      $Qdate = $CLICSHOPPING_StatsMarginReport->db->query("SELECT FROM_UNIXTIME(" . strtotime(date("F 1, Y")) . ") as time");
      $d = $Qdate->fetch();

      $Qdate = $CLICSHOPPING_StatsMarginReport->db->query("SELECT curdate() as time");
      $d2 = $Qdate->fetch();

      $header = $CLICSHOPPING_StatsMarginReport->getDef('text_report_header_from_month');
      break;
    case 'lastmonth':

      $Qdate = $CLICSHOPPING_StatsMarginReport->db->query("SELECT FROM_UNIXTIME(" . strtotime(date("F 1, Y")) . ") - INTERVAL 1 MONTH as time");
      $d = $Qdate->fetch();

      $Qdate = $CLICSHOPPING_StatsMarginReport->db->query("SELECT FROM_UNIXTIME(" . strtotime(date("F 1, Y")) . ") - INTERVAL 0 MONTH as time");
      $d2 = $Qdate->fetch();

      $header = $CLICSHOPPING_StatsMarginReport->getDef('text_report_header_from_last_month');
      break;
    case 'quarterly':

      $Qquarter = $CLICSHOPPING_StatsMarginReport->db->query("SELECT QUARTER(now()) as quarter, year(now()) as year");
      $quarter = $Qquarter->fetch();

      switch ($quarter['quarter']) {
        case '1':
          $d['time'] = $quarter['year'] . '-01-01';
          break;
        case '2':
          $d['time'] = $quarter['year'] . '-04-01';
          break;
        case '3':
          $d['time'] = $quarter['year'] . '-07-01';
          break;
        case '4':
          $d['time'] = $quarter['year'] . '-10-01';
          break;
      }

      $header = $CLICSHOPPING_StatsMarginReport->getDef('text_report_header_from_quarter');
      break;
    case 'semiannually':
      $Qyear = $CLICSHOPPING_StatsMarginReport->db->query("SELECT year(now()) as year, month(now()) as month");
      $year = $Qyear->fetch();

      if ($year['month'] >= '7') {
        $d['time'] = $year['year'] . '-07-01';
      } else {
        $d['time'] = $year['year'] . '-01-01';
      }

      $header = $CLICSHOPPING_StatsMarginReport->getDef('text_report_headerfrom_semiyear');
      break;
    case 'annually':
      $Qyear = $CLICSHOPPING_StatsMarginReport->db->query("SELECT year(now()) as year");
      $year = $Qyear->fetch();

      $d['time'] = $year['year'] . '-01-01';
      $header = $CLICSHOPPING_StatsMarginReport->getDef('text_report_header_from_year');
      break;

  }

  // show orders with selected status
  if (isset($_POST['status']) && is_numeric($_POST['status']) && ($_POST['status'] > 0)) {
    $status = HTML::sanitize($_POST['status']);

    if (isset($_POST['date'])) {

      $header = $CLICSHOPPING_StatsMarginReport->getDef('text_report_between_days') . ' ' . $_POST['sdate'] . ' ' . $CLICSHOPPING_StatsMarginReport->getDef('text_and') . ' ' . $_POST['edate'] . ': ';

      $date_debut = explode("/", $_POST['sdate']);
      $dd1 = $date_debut[2] . '-' . $date_debut[1] . '-' . $date_debut[0];

      $date_fin = explode("/", $_POST['edate']);
      $df1 = $date_fin[2] . '-' . $date_fin[1] . '-' . $date_fin[0];

      $Qorder = $CLICSHOPPING_StatsMarginReport->db->prepare('select orders_id
                                                        from :table_orders
                                                        where date_purchased > :date_purchased
                                                        and date_purchased < :date_purchased1
                                                        and orders_status = :orders_status
                                                        order by orders_id asc
                                                      ');
      $Qorder->bindValue(':date_purchased', $dd1);
      $Qorder->bindValue(':date_purchased1', $df1);
      $Qorder->bindInt(':orders_status', (int)$status);
      $Qorder->execute();


    } else {
      if ($report != '1') {

        $QheaderDate = $CLICSHOPPING_StatsMarginReport->db->query("select date_format('" . $d['time'] . "', '%d/%m/%Y') as date");
        $header_date = $QheaderDate->fetch();
        $header .= $header_date['date'];

        if (isset($d2)) {
// have date range, use it

          $Qorder = $CLICSHOPPING_StatsMarginReport->db->prepare('select orders_id
                                                            from :table_orders
                                                            where date_purchased > :date_purchased
                                                            and date_purchased < :date_purchased1
                                                            and orders_status = :orders_status
                                                            order by orders_id asc
                                                            ');
          $Qorder->bindValue(':date_purchased', $d['time']);
          $Qorder->bindValue(':date_purchased1', $d2['time']);
          $Qorder->bindInt(':orders_status', (int)$status);
          $Qorder->execute();

        } else {
// don't have a d2, business as usual

          $Qorder = $CLICSHOPPING_StatsMarginReport->db->prepare('select orders_id
                                                            from :table_orders
                                                            where date_purchased > :date_purchased
                                                            and date_purchased < now()
                                                            and orders_status = :orders_status
                                                            order by orders_id asc
                                                            ');
          $Qorder->bindValue(':date_purchased', $d['time']);
          $Qorder->bindInt(':orders_status', (int)$status);
          $Qorder->execute();
        }
      }
    }
// show all orders
  } elseif (isset($_POST['date'])) {
    $header = $CLICSHOPPING_StatsMarginReport->getDef('text_report_between_days') . ' ' . $_POST['sdate'] . ' ' . $CLICSHOPPING_StatsMarginReport->getDef('text_and') . ' ' . $_POST['edate'] . ': ';
    $date_debut = explode("/", $_POST['sdate']);
    $dd1 = $date_debut[2] . '-' . $date_debut[1] . '-' . $date_debut[0];
    $date_fin = explode("/", $_POST['edate']);
    $df1 = $date_fin[2] . '-' . $date_fin[1] . '-' . $date_fin[0];

    $Qorder = $CLICSHOPPING_StatsMarginReport->db->prepare('select orders_id
                                                     from :table_orders
                                                     where date_purchased > :date_purchased
                                                     and date_purchased < :date_purchased1
                                                     order by orders_id asc
                                                    ');
    $Qorder->bindValue(':date_purchased', $dd1);
    $Qorder->bindValue(':date_purchased1', $df1);
    $Qorder->execute();

  } else {
    if ($report != '1') {
      $QheaderDate = $CLICSHOPPING_StatsMarginReport->db->query("SELECT DATE_FORMAT('" . $d2['time'] . "', '%d/%m/%Y') as date");
      $header_date = $QheaderDate->fetch();
      $header .= $header_date['date'];

      if (isset($d2)) {
// have date range, use it
        $Qorder = $CLICSHOPPING_StatsMarginReport->db->prepare('select orders_id
                                                         from :table_orders
                                                         where date_purchased > :date_purchased
                                                         and date_purchased < :date_purchased1
                                                         order by orders_id asc
                                                        ');
        $Qorder->bindValue(':date_purchased', $d['time']);
        $Qorder->bindValue(':date_purchased1', $d2['time']);
        $Qorder->execute();

      } else {

// don't have a d2, business as usual
        $Qorder = $CLICSHOPPING_StatsMarginReport->db->prepare('select orders_id
                                                               from :table_orders
                                                               where date_purchased > :date_purchased
                                                               and date_purchased < now()
                                                               order by orders_id asc
                                                              ');
        $Qorder->bindValue(':date_purchased', $d['time']);
        $Qorder->execute();
      }
    } else {
      $Qorder = $CLICSHOPPING_StatsMarginReport->db->query('select orders_id 
                                                            from table_orders 
                                                            order by orders_id 
                                                            asc
                                                            ');
    }
  }

  $o = [];

  while ($Qorder->fetch()) {
    $o[] = $Qorder->valueInt('orders_id');
  }

  $p = [];
  $total_price = 0;
  $total_cost = 0;
  $total_items_sold = 0;
  $t = '0';
?>

<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/margin_report.png', $CLICSHOPPING_StatsMarginReport->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-5 pageHeading"><?php echo $CLICSHOPPING_StatsMarginReport->getDef('heading_title'); ?></span>
          <span class="col-md-3">
              <div>
<?php

  if (isset($report)) {
    $option = $report;
  } else {
    $option = null;
  }

  echo HTML::form('report', $CLICSHOPPING_StatsMarginReport->link('StatsMarginReport&' . $option), 'post');

  $options = [];
  $options[] = ['id' => 'all', 'text' => $CLICSHOPPING_StatsMarginReport->getDef('text_select_report')];
  $options[] = ['id' => 'daily', 'text' => $CLICSHOPPING_StatsMarginReport->getDef('text_select_report_daily')];
  $options[] = ['id' => 'yesterday', 'text' => $CLICSHOPPING_StatsMarginReport->getDef('text_select_report_yesterday')];
  $options[] = ['id' => 'weekly', 'text' => $CLICSHOPPING_StatsMarginReport->getDef('text_select_report_weekly')];
  $options[] = ['id' => 'lastweek', 'text' => $CLICSHOPPING_StatsMarginReport->getDef('text_select_report_last_week')];
  $options[] = ['id' => 'monthly', 'text' => $CLICSHOPPING_StatsMarginReport->getDef('text_select_report_monthly')];
  $options[] = ['id' => 'lastmonth', 'text' => $CLICSHOPPING_StatsMarginReport->getDef('text_select_report_last_month')];
  $options[] = ['id' => 'quarterly', 'text' => $CLICSHOPPING_StatsMarginReport->getDef('text_select_report_quarterly')];
  $options[] = ['id' => 'semiannually', 'text' => $CLICSHOPPING_StatsMarginReport->getDef('text_select_report_semiannually')];
  $options[] = ['id' => 'annually', 'text' => $CLICSHOPPING_StatsMarginReport->getDef('text_select_report_annually')];

  echo HTML::selectMenu('report_id', $options, (isset($report) ? $report : '1'), 'onchange="this.form.submit()"');
?>
              </div>
              </span>
          <span class="col-md-3">
                <div>
                  <?php echo HTML::selectMenu('status', array_merge(array(array('id' => '', 'text' => $CLICSHOPPING_StatsMarginReport->getDef('text_all_orders'))), $orders_statuses), '', 'onChange="this.form.submit();"'); ?>
                </div>
              </span>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <div>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <td>
        <table class="table table-sm table-hover table-striped">
          <thead>
          <tr class="dataTableHeadingRow">
            <th width="100"><?php echo $CLICSHOPPING_StatsMarginReport->getDef('table_heading_order_id'); ?></th>
            <th><?php echo $CLICSHOPPING_StatsMarginReport->getDef('table_heading_item_sold'); ?></th>
            <th><?php echo $CLICSHOPPING_StatsMarginReport->getDef('table_heading_sales_amount'); ?></th>
            <th><?php echo $CLICSHOPPING_StatsMarginReport->getDef('table_heading_reduc'); ?></th>
            <th><?php echo $CLICSHOPPING_StatsMarginReport->getDef('table_heading_sales_net'); ?></th>
            <th><?php echo $CLICSHOPPING_StatsMarginReport->getDef('table_heading_cost'); ?></th>
            <th><?php echo $CLICSHOPPING_StatsMarginReport->getDef('table_heading_handling'); ?></th>
            <th><?php echo $CLICSHOPPING_StatsMarginReport->getDef('table_heading_gross_profit'); ?></th>
            <th><?php echo $CLICSHOPPING_StatsMarginReport->getDef('table_heading_profit_net'); ?></th>
            <th><?php echo $CLICSHOPPING_StatsMarginReport->getDef('table_heading_action'); ?></th>
          </tr>
          </thead>
          <tbody>
          <?php
            $total_reduc = 0;
            $total_price = 0;
            $total_cost = 0;
            $total_margin = 0;
            $total_handling = 0;
            $total_margin_net = 0;

            for ($i = 0;
            $i < count($o);
            $i++) {
            $price = 0;
            $cost = 0;
            $handling = 0;
            $items_sold = 0;

            $Qprods = $CLICSHOPPING_StatsMarginReport->db->prepare('select op.products_id,
                                                             op.products_price,
                                                             op.final_price,
                                                             op.products_quantity,
                                                             p.products_cost,
                                                             p.products_handling
                                                      from :table_orders_products op,
                                                           :table_products p
                                                      where op.orders_id = :orders_id
                                                      and op.products_id = p.products_id
                                                    ');
            $Qprods->bindInt(':orders_id', $o[$i]);
            $Qprods->execute();

            while ($Qprods->fetch()) {
              $p[] = [$Qprods->valueInt('products_id'),
                $Qprods->valueDecimal['products_price'],
                $Qprods->valueDecimal['products_cost'],
                $Qprods->valueDecimal['products_handling'],
                $Qprods->valueInt('products_quantity')
              ];

              $QpromoDiscountCoupon = $CLICSHOPPING_StatsMarginReport->db->prepare('select value
                                                                     from :table_orders_total
                                                                     where orders_id = :orders_id
                                                                     and (class = :ot_discount_coupon or  class = :ot_discount_coupon1)
                                                                    ');
              $QpromoDiscountCoupon->bindInt(':orders_id', $o[$i]);
              $QpromoDiscountCoupon->bindValue(':ot_discount_coupon', 'ot_discount_coupon');
              $QpromoDiscountCoupon->bindValue(':ot_discount_coupon1', 'DC');
              $QpromoDiscountCoupon->execute();

              $items_sold += $Qprods->valueInt('products_quantity');
              $price += $Qprods->valueDecimal('products_price') * $Qprods->valueInt('products_quantity');
              $cost += $Qprods->valueDecimal('products_cost') * $Qprods->valueInt('products_quantity');
              $handling += $Qprods->valueDecimal('products_handling') * $Qprods->valueInt('products_quantity');
              $reduc += $QpromoDiscountCoupon->valueDecimal('value') * $Qprods->valueInt('products_quantity');

              if ($cost < 0) {
                $cost = $cost * -1;
              }

              if ($handling < 0) {
                $handling = $handling * -1;
              }

              if ($reduc < 0) {
                $reduc = $reduc * -1;
              }

              $total_items_sold += $Qprods->valueInt('products_quantity');
              $total_price += $Qprods->valueDecimal('products_price') * $Qprods->valueInt('products_quantity');
              $total_reduc += $Qprods->valueDecimal('value') * $Qprods->valueInt('products_quantity');

              if ($cost < 0) {
                $cost = $cost * -1;
              }

              if ($total_handling < 0) {
                $total_handling = $total_handling * -1;
              }


              if ($total_cost < 0) {
                $total_cost = $total_cost * -1;
              }


              $total_handling += $Qprods->valueDecimal('products_handling') * $Qprods->valueInt('products_quantity');
              $total_cost += $Qprods->valueDecimal('products_cost') * $Qprods->valueInt('products_quantity');

// the following two lines will give us per order margin as well as the total margin
              if ($Qprods->valueDecimal('products_price') != 0) {
                $margin = round(((($Qprods->valueDecimal('products_price') - $Qprods->valueDecimal('products_cost')) / $Qprods->valueDecimal('products_price')) * 100), 0);
                $margin_net = round((((($Qprods->valueDecimal('products_price') + ($QpromoDiscountCoupon->valueDecimal('value') - $Qprods->valueDecimal('products_cost') - $Qprods->valueDecimal('products_handling')))) / $Qprods->valueDecimal('products_price')) * 100), 0);

                $total_margin = round(((($total_price - $total_cost) / $total_price) * 100), 2);
                $total_margin_net = round(((($total_price - $total_reduc - $total_cost - $total_handling) / $total_price) * 100), 2);
              }
            } // end while
          ?>
          <tr onMouseOver="rowOverEffect(this)" onMouseOut="rowOutEffect(this)"
          ">
          <th scope="row">
          <?php echo $o[$i]; ?></td>
          <td><?php echo $items_sold; ?></td>
          <td><?php echo $CLICSHOPPING_Currencies->format($price); ?></td>
          <td><?php echo $CLICSHOPPING_Currencies->format($reduc); ?></td>
          <td><?php echo $CLICSHOPPING_Currencies->format(($price - $reduc)); ?></td>
          <td><?php echo $CLICSHOPPING_Currencies->format($cost); ?></td>
          <td><?php echo $CLICSHOPPING_Currencies->format($handling); ?></td>
          <td><?php echo $CLICSHOPPING_Currencies->format(($price - $cost)) . ' (' . $margin . '%)'; ?></td>
          <td><?php echo $CLICSHOPPING_Currencies->format($price - $reduc - $cost - $handling) . ' (' . $margin_net . '%)'; ?></td>
          <td
            class="text-end"><?php echo '<a href=' . CLICSHOPPING::link(null, 'A&Orders\Orders&Edit&oID=' . $o[$i]) . '>' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_StatsMarginReport->getDef('icon_edit')); ?></a></td>
          </tr>
          </tbody>
          <?php
            } // end for
          ?>
          <thead>
          <tr>
            <td class="main" colspan="2" bgcolor="#C8FCCA">
              <strong><?php echo $CLICSHOPPING_StatsMarginReport->getDef('text_total_items_sold'); ?></strong></td>
            <td class="main" colspan="2" bgcolor="#E0E0E0">&nbsp;<?php echo $total_items_sold; ?></td>
          </tr>
          <tr>
            <td class="main" colspan="2" bgcolor="#C8FCCA">
              <strong><?php echo $CLICSHOPPING_StatsMarginReport->getDef('text_items_sold'); ?></strong></td>
            <td class="main" colspan="2" bgcolor="#E0E0E0">
              &nbsp;<?php echo $CLICSHOPPING_Currencies->format($total_price); ?></td>
          </tr>
          <tr>
            <td class="main" colspan="2" bgcolor="#C8FCCA">
              <strong><?php echo $CLICSHOPPING_StatsMarginReport->getDef('text_total_reduc'); ?></strong></td>
            <td class="main" colspan="2" bgcolor="#E0E0E0">
              &nbsp;<?php echo $CLICSHOPPING_Currencies->format($total_reduc); ?></td>
          </tr>
          <tr>
            <td class="main" colspan="2" bgcolor="#C8FCCA">
              <strong><?php echo $CLICSHOPPING_StatsMarginReport->getDef('text_sales_net'); ?></strong></td>
            <td class="main" colspan="2" bgcolor="#E0E0E0">
              &nbsp;<?php echo $CLICSHOPPING_Currencies->format(($total_price - $total_reduc)); ?></td>
          </tr>
          <tr>
            <td class="main" colspan="2" bgcolor="#C8FCCA">
              <strong><?php echo $CLICSHOPPING_StatsMarginReport->getDef('text_total_cost'); ?></strong></td>
            <td class="main" colspan="2" bgcolor="#E0E0E0">
              &nbsp;<?php echo $CLICSHOPPING_Currencies->format($total_cost); ?></td>
          </tr>
          <tr>
            <td class="main" colspan="2" bgcolor="#C8FCCA">
              <strong><?php echo $CLICSHOPPING_StatsMarginReport->getDef('text_total_gross_profit'); ?></strong></td>
            <td class="main" colspan="2" bgcolor="#E0E0E0">
              &nbsp;<?php echo $CLICSHOPPING_Currencies->format(($total_price - $total_cost)) . ' (' . $total_margin . '%)'; ?></td>
          </tr>
          <tr>
            <td class="main" colspan="2" bgcolor="#C8FCCA">
              <strong><?php echo $CLICSHOPPING_StatsMarginReport->getDef('text_total_margin_net'); ?></strong></td>
            <td class="main" colspan="2" bgcolor="#E0E0E0">
              &nbsp;<?php echo $CLICSHOPPING_Currencies->format(($total_price - $total_cost - $total_reduc - $total_handling)) . ' (' . $total_margin_net . '%)'; ?></td>
          </tr>
          </thead>
        </table>
      </td>
      </tr>
    </table>

    <div class="separator"></div>
    <div class="alert alert-info" role="alert">
      <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_StatsMarginReport->getDef('title_help_margin_image')) . ' ' . $CLICSHOPPING_StatsMarginReport->getDef('title_help_margin_image') ?></div>
      <div class="separator"></div>
      <div><?php echo $CLICSHOPPING_StatsMarginReport->getDef('title_help_margin_description'); ?></div>
    </div>
  </div>
</div>
</div>


