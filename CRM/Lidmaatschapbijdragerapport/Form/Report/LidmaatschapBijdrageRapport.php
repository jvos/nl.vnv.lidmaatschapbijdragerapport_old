<?php
/**
 * Lidmaatschap Bijdrage Rapport
 * BOSW1508247 vnv.nl - exporteren data clubcollect
 * 
 * This report is a copy of the CRM/Report/Form/Contribute/Summary report.
 * With a view changes for the call BOSW1508247 vnv.nl - exporteren data clubcollect
 */
class CRM_Lidmaatschapbijdragerapport_Form_Report_LidmaatschapBijdrageRapport extends CRM_Report_Form {

  protected $_addressField = FALSE;

  protected $_charts = array(
    '' => 'Tabular',
    'barChart' => 'Bar Chart',
    'pieChart' => 'Pie Chart',
  );
  protected $_customGroupExtends = array('Contribution');
  protected $_customGroupGroupBy = TRUE;

  public $_drilldownReport = array('contribute/detail' => 'Link to Detail Report');

  function __construct() {

  // Check if CiviCampaign is a) enabled and b) has active campaigns
  $config = CRM_Core_Config::singleton();
    $campaignEnabled = in_array("CiviCampaign", $config->enableComponents);
    if ($campaignEnabled) {
      $getCampaigns = CRM_Campaign_BAO_Campaign::getPermissionedCampaigns(NULL, NULL, TRUE, FALSE, TRUE);
      $this->activeCampaigns = $getCampaigns['campaigns'];
      asort($this->activeCampaigns);
    }
    
    $this->_columns = array(
      'civicrm_contact' =>
      array(
        'dao' => 'CRM_Contact_DAO_Contact',
        'fields' =>
        array(
          'sort_name' =>
          array(
            'title' => ts('Contact Name'),
            'no_repeat' => TRUE,
            'default' => TRUE,
          ),
          'first_name' => array(
            'title' => ts('First Name'),
            'default' => TRUE,
          ),
          'middle_name' => array(
            'title' => ts('Middle Name'),
            'default' => TRUE,
          ),
          'last_name' => array(
            'title' => ts('Last Name'),
            'default' => TRUE,
          ),
          'postal_greeting_display' =>
          array('title' => ts('Postal Greeting')),
          'id' =>
          array(
            'no_display' => TRUE,
            'required' => TRUE,
          ),
          'contact_type' =>
          array(
            'title' => ts('Contact Type'),
          ),
          'contact_sub_type' =>
          array(
            'title' => ts('Contact SubType'),
          ),
        ),
        'order_bys' =>
        array(
          'sort_name' =>
          array(
            'title' => ts('Contact Name'), 
            'default' => TRUE, 
            'default_weight' => '0', 
            'default_order' => 'ASC'
          ),
        ),
        'grouping' => 'contact-fields',
        'group_bys' =>
        array(
          'id' =>
          array('title' => ts('Contact ID'), 'default' => TRUE),
          'sort_name' =>
          array('title' => ts('Contact Name'),
          ),
        ),
      ),
      'civicrm_email' =>
      array(
        'dao' => 'CRM_Core_DAO_Email',
        'fields' =>
        array(
          'email' =>
          array('title' => ts('Email'),
            'no_repeat' => TRUE,
            'default' => TRUE,
          ),
        ),
        'grouping' => 'contact-fields',
      ),
      'civicrm_phone' =>
      array(
        'dao' => 'CRM_Core_DAO_Phone',
        'fields' =>
        array(
          'phone' =>
          array('title' => ts('Phone'),
            'no_repeat' => TRUE,
            'default' => TRUE,
          ),
        ),
        'grouping' => 'contact-fields',
      ),
      'civicrm_financial_type' =>
      array('dao' => 'CRM_Financial_DAO_FinancialType',
        'fields' => array('financial_type' => null,),
        'grouping' => 'contri-fields',
        'group_bys' => array(
          'financial_type' => array('title' => ts('Financial Type')),
        ),
      ),
      'civicrm_contribution' =>
      array(
        'dao' => 'CRM_Contribute_DAO_Contribution',
        //'bao'           => 'CRM_Contribute_BAO_Contribution',
        'fields' =>
        array(
          'contribution_source' => array('title' => ts('Source'), ),
          'currency' =>
          array('required' => TRUE,
            'no_display' => TRUE,
          ),
          'total_amount' =>
          array('title' => ts('Contribution Amount Stats'),
            'default' => TRUE,
            'statistics' =>
            array('sum' => ts('Contributions Aggregate'),
              'count' => ts('Contributions'),
              'avg' => ts('Contributions Avg'),
            ),
          ),
        ),
        'grouping' => 'contri-fields',
        'filters' =>
        array(
          'receive_date' =>
          array('title' => ts('Receive Date'), 'operatorType' => CRM_Report_Form::OP_DATE, 'default' => '0'),
          'contribution_status_id' =>
          array('title' => ts('Donation Status'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Contribute_PseudoConstant::contributionStatus(),
            'default' => array(1),
            'type' => CRM_Utils_Type::T_INT,
          ),
          'currency' =>
          array('title' => 'Currency',
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Core_OptionGroup::values('currencies_enabled'),
            'default' => NULL,
            'type' => CRM_Utils_Type::T_STRING,
          ),
          'financial_type_id' =>
          array('title' => ts('Financial Type'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options'  => CRM_Contribute_PseudoConstant::financialType(),
            'type' => CRM_Utils_Type::T_INT,
          ),
          'contribution_page_id' =>
          array('title' => ts('Contribution Page'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options'  => CRM_Contribute_PseudoConstant::contributionPage(),
            'type' => CRM_Utils_Type::T_INT,
          ),
          'total_amount' =>
          array('title' => ts('Contribution Amount'),
          ),
          'total_sum' =>
          array('title' => ts('Contributions Aggregate'),
            'type' => CRM_Report_Form::OP_INT,
            'dbAlias' => 'civicrm_contribution_total_amount_sum',
            'having' => TRUE,
          ),
          'total_count' =>
          array('title' => ts('Contributions Count'),
            'type' => CRM_Report_Form::OP_INT,
            'dbAlias' => 'civicrm_contribution_total_amount_count',
            'having' => TRUE,
          ),
          'total_avg' =>
          array('title' => ts('Contributions Avg'),
            'type' => CRM_Report_Form::OP_INT,
            'dbAlias' => 'civicrm_contribution_total_amount_avg',
            'having' => TRUE,
          ),
        ),
        'group_bys' =>
        array(
          'receive_date' =>
          array(
            'frequency' => TRUE,
            'default' => TRUE,
            'chart' => TRUE,
          ),
          'contribution_source' => NULL,
        ),
      ),
      'civicrm_contribution_soft' =>
      array(
        'dao' => 'CRM_Contribute_DAO_ContributionSoft',
        'fields' =>
        array(
          'soft_amount' =>
          array(
            'title' => ts('Soft Credit Amount Stats'),
            'name'  => 'amount',
            'statistics' =>
            array('sum' => ts('Soft Credit Aggregate'),
              'count' => ts('Soft Credits'),
              'avg' => ts('Soft Credit Avg'),
            ),
          ),
        ),
        'grouping' => 'contri-fields',
        'filters' =>
        array(
          'amount' =>
          array('title' => ts('Soft Credit Amount'),
          ),
          'soft_sum' =>
          array('title' => ts('Soft Credit Aggregate'),
            'type' => CRM_Report_Form::OP_INT,
            'dbAlias' => 'civicrm_contribution_soft_soft_amount_sum',
            'having' => TRUE,
          ),
          'soft_count' =>
          array('title' => ts('Soft Credits Count'),
            'type' => CRM_Report_Form::OP_INT,
            'dbAlias' => 'civicrm_contribution_soft_soft_amount_count',
            'having' => TRUE,
          ),
          'soft_avg' =>
          array('title' => ts('Soft Credit Avg'),
            'type' => CRM_Report_Form::OP_INT,
            'dbAlias' => 'civicrm_contribution_soft_soft_amount_avg',
            'having' => TRUE,
          ),
        ),
      ),
      'civicrm_group' =>
      array(
        'dao' => 'CRM_Contact_DAO_GroupContact',
        'alias' => 'cgroup',
        'filters' =>
        array(
          'gid' =>
          array(
            'name' => 'group_id',
            'title' => ts('Group'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'group' => TRUE,
            'options' => CRM_Core_PseudoConstant::group(),
            'type' => CRM_Utils_Type::T_INT,
          ),
        ),
      ),
    ) + $this->addAddressFields();
            
    // If we have a campaign, build out the relevant elements
    $this->_tagFilter = TRUE;
    if ($campaignEnabled && !empty($this->activeCampaigns)) {
      $this->_columns['civicrm_contribution']['fields']['campaign_id'] = array(
        'title' => 'Campaign',
        'default' => 'false',
      );
      $this->_columns['civicrm_contribution']['filters']['campaign_id'] = array('title' => ts('Campaign'),
        'operatorType' => CRM_Report_Form::OP_MULTISELECT,
        'options' => $this->activeCampaigns,
      );
      $this->_columns['civicrm_contribution']['group_bys']['campaign_id'] = array('title' => ts('Campaign'));
    }

    /*
     * BOSW1508247 vnv.nl - exporteren data clubcollect
     * Hack uncheck land field
     */
    $this->_columns['civicrm_address']['fields']['street_address']['default'] = TRUE;    
    $this->_columns['civicrm_address']['fields']['supplemental_address_1']['default'] = TRUE;    
    $this->_columns['civicrm_address']['fields']['supplemental_address_2']['default'] = TRUE;    
    $this->_columns['civicrm_address']['fields']['postal_code']['default'] = TRUE;    
    $this->_columns['civicrm_address']['fields']['city']['default'] = TRUE; 
    
    $this->_currencyColumn = 'civicrm_contribution_currency';
    parent::__construct();
    
    /*
     * BOSW1508247 vnv.nl - exporteren data clubcollect
     * Add country name or the country id will show in the Preview Excel 
     */
    // hide country_id field
    $this->_columns['civicrm_address']['fields']['country_id'] = array(
      'no_display' => TRUE,
      'required' => TRUE,
    );
    
    // add country field    
    $this->_columns['civicrm_country'] = array(
      'dao' => 'CRM_Core_DAO_County',
      'grouping' => 'location-fields',
      'fields' => array(
        'civicrm_country_name' => array(
          'title' => ts('Country'),
          'default' => TRUE,
          'dbAlias' => 'country_civireport.name',
        ),
      ),
    );
    
    /*
     * BOSW1508247 vnv.nl - exporteren data clubcollect
     * Membership
     */
    // add membership for lidmaatschap
    $this->_columns['civicrm_membership'] = array(
      'dao' => 'CRM_Member_DAO_Membership',
      'grouping' => 'civicrm_membership',
      'group_title' => ts('Membership'),
      'fields' => array(
        'id' => array(
          'no_display' => TRUE,
          'title' => ts('Membership Id'),
          'required' => TRUE,
          'no_repeat' => TRUE,
        ),
        'membership_type_id' => array(
          'no_display' => TRUE,
          'title' => ts('Membership Type'),
          'default' => TRUE,
          'required' => TRUE,
        ),
        'membership_start_date' => array('title' => ts('Start Date'),
          //'default' => TRUE,
        ),
        'membership_end_date' => array('title' => ts('End Date'),
          //'default' => TRUE,
        ),
        'join_date' => array('title' => ts('Join Date'),
          //'default' => TRUE,
        ),
        'source' => array('title' => 'Source'),
      ),
    );
    
    $this->_columns['civicrm_membership_type'] = array(
      'dao' => 'CRM_Member_DAO_MembershipType',
      'grouping' => 'civicrm_membership',
      'group_title' => ts('Membership'),
      'fields' => array(
        'name' => array(
          'title' => ts('Membership Type'),
          'default' => TRUE,
        ),
      ),
    );
        
    /*
     * BOSW1508247 vnv.nl - exporteren data clubcollect
     * Lidmaatschap - Maatschappij
     */
    $lidmaatschapbijdrage_config = CRM_Lidmaatschapbijdragerapport_Config::singleton();
    $lidmaatschap_custom_group = $lidmaatschapbijdrage_config->get_lidmaatschap_custom_group();
    
    // add the Lidmaatschap - Maatschappij
    $this->_columns[$lidmaatschap_custom_group['table_name']] = array(
      'dao' => 'CRM_Contact_DAO_Contact',
      'extends' => 'Membership', 
      'grouping' => $lidmaatschap_custom_group['table_name'],
      'group_title' => $lidmaatschap_custom_group['title'],
      'fields' => Array(),
    );
    
    // add the Lidmaatschap - Maatschappij fields
    foreach($lidmaatschapbijdrage_config->get_lidmaatschap_custom_fields() as $custom_field_id => $custom_field){      
      $this->_columns[$lidmaatschap_custom_group['table_name']]['fields']['custom_' . $custom_field_id] = array(
        'name' => strtolower($custom_field['name']) . '_' . $custom_field_id,
        'title' => $custom_field['label'],
        //'default' => TRUE,
      );
      
      // check only lidnr and 
      if('lidnr' == $custom_field['name']){
        $this->_columns[$lidmaatschap_custom_group['table_name']]['fields']['custom_' . $custom_field_id]['default'] = TRUE;
      }
      
    }    
  }

  function preProcess() {
    parent::preProcess();
  }

  function setDefaultValues($freeze = TRUE) {
    $defaults = parent::setDefaultValues($freeze);
    /*
     * BOSW1508247 vnv.nl - exporteren data clubcollect
     * Set default frequency to Year, and
     * receive date to choose
     */
    $defaults['group_bys_freq']['receive_date'] = 'YEAR';
    $defaults['filters']['receive_date_relative'] = '0';
    return $defaults; 
  }

  function select() {
    $select = array();
    $this->_columnHeaders = array();
    foreach ($this->_columns as $tableName => $table) {
      if (array_key_exists('group_bys', $table)) {
        foreach ($table['group_bys'] as $fieldName => $field) {
          if ($tableName == 'civicrm_address') {
            $this->_addressField = TRUE;
          }
          if (CRM_Utils_Array::value($fieldName, $this->_params['group_bys'])) {
            switch (CRM_Utils_Array::value($fieldName, $this->_params['group_bys_freq'])) {
              case 'YEARWEEK':
                $select[]       = "DATE_SUB({$field['dbAlias']}, INTERVAL WEEKDAY({$field['dbAlias']}) DAY) AS {$tableName}_{$fieldName}_start";
                $select[]       = "YEARWEEK({$field['dbAlias']}) AS {$tableName}_{$fieldName}_subtotal";
                $select[]       = "WEEKOFYEAR({$field['dbAlias']}) AS {$tableName}_{$fieldName}_interval";
                $field['title'] = 'Week';
                break;

              case 'YEAR':
                $select[]       = "MAKEDATE(YEAR({$field['dbAlias']}), 1)  AS {$tableName}_{$fieldName}_start";
                $select[]       = "YEAR({$field['dbAlias']}) AS {$tableName}_{$fieldName}_subtotal";
                $select[]       = "YEAR({$field['dbAlias']}) AS {$tableName}_{$fieldName}_interval";
                $field['title'] = 'Year';
                break;

              case 'MONTH':
                $select[]       = "DATE_SUB({$field['dbAlias']}, INTERVAL (DAYOFMONTH({$field['dbAlias']})-1) DAY) as {$tableName}_{$fieldName}_start";
                $select[]       = "MONTH({$field['dbAlias']}) AS {$tableName}_{$fieldName}_subtotal";
                $select[]       = "MONTHNAME({$field['dbAlias']}) AS {$tableName}_{$fieldName}_interval";
                $field['title'] = 'Month';
                break;

              case 'QUARTER':
                $select[]       = "STR_TO_DATE(CONCAT( 3 * QUARTER( {$field['dbAlias']} ) -2 , '/', '1', '/', YEAR( {$field['dbAlias']} ) ), '%m/%d/%Y') AS {$tableName}_{$fieldName}_start";
                $select[]       = "QUARTER({$field['dbAlias']}) AS {$tableName}_{$fieldName}_subtotal";
                $select[]       = "QUARTER({$field['dbAlias']}) AS {$tableName}_{$fieldName}_interval";
                $field['title'] = 'Quarter';
                break;
            }
            if (CRM_Utils_Array::value($fieldName, $this->_params['group_bys_freq'])) {
              $this->_interval = $field['title'];
              $this->_columnHeaders["{$tableName}_{$fieldName}_start"]['title'] = $field['title'] . ' Beginning';
              $this->_columnHeaders["{$tableName}_{$fieldName}_start"]['type'] = $field['type'];
              $this->_columnHeaders["{$tableName}_{$fieldName}_start"]['group_by'] = $this->_params['group_bys_freq'][$fieldName];

              // just to make sure these values are transfered to rows.
              // since we need that for calculation purpose,
              // e.g making subtotals look nicer or graphs
              $this->_columnHeaders["{$tableName}_{$fieldName}_interval"] = array('no_display' => TRUE);
              $this->_columnHeaders["{$tableName}_{$fieldName}_subtotal"] = array('no_display' => TRUE);
            }
          }
        }
      }

      if (array_key_exists('fields', $table)) {
        foreach ($table['fields'] as $fieldName => $field) {          
          if ($tableName == 'civicrm_address') {
            $this->_addressField = TRUE;
          }
          if (CRM_Utils_Array::value('required', $field) ||
            CRM_Utils_Array::value($fieldName, $this->_params['fields'])
          ) {

            // only include statistics columns if set
            if (CRM_Utils_Array::value('statistics', $field)) {
              foreach ($field['statistics'] as $stat => $label) {
                switch (strtolower($stat)) {
                  case 'sum':
                    $select[] = "SUM({$field['dbAlias']}) as {$tableName}_{$fieldName}_{$stat}";
                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['title'] = $label;
                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['type'] = $field['type'];
                    $this->_statFields[] = "{$tableName}_{$fieldName}_{$stat}";
                    break;

                  case 'count':
                    $select[] = "COUNT({$field['dbAlias']}) as {$tableName}_{$fieldName}_{$stat}";
                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['type'] = CRM_Utils_Type::T_INT;
                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['title'] = $label;
                    $this->_statFields[] = "{$tableName}_{$fieldName}_{$stat}";
                    break;

                  case 'avg':
                    $select[] = "ROUND(AVG({$field['dbAlias']}),2) as {$tableName}_{$fieldName}_{$stat}";
                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['type'] = $field['type'];
                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['title'] = $label;
                    $this->_statFields[] = "{$tableName}_{$fieldName}_{$stat}";
                    break;
                }
              }
            }
            else {
              $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
              $this->_columnHeaders["{$tableName}_{$fieldName}"]['type'] = CRM_Utils_Array::value('type', $field);
              $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = CRM_Utils_Array::value('title', $field);
            }
          }
        }
      }
    }

    $this->_select = "SELECT " . implode(', ', $select) . " ";
    
    
  }

  static function formRule($fields, $files, $self) {
    $errors = $grouping = array();
    //check for searching combination of dispaly columns and
    //grouping criteria
    $ignoreFields = array('total_amount', 'sort_name');
    $errors = $self->customDataFormRule($fields, $ignoreFields);

    if (!CRM_Utils_Array::value('receive_date', $fields['group_bys'])) {
      if (CRM_Utils_Array::value('receive_date_relative', $fields) ||
        CRM_Utils_Date::isDate($fields['receive_date_from']) ||
        CRM_Utils_Date::isDate($fields['receive_date_to'])
      ) {
        $errors['receive_date_relative'] = ts("Do not use filter on Date if group by Receive Date is not used ");
      }
    }
    if (!CRM_Utils_Array::value('total_amount', $fields['fields'])) {
      foreach (array(
        'total_count_value', 'total_sum_value', 'total_avg_value') as $val) {
        if (CRM_Utils_Array::value($val, $fields)) {
          $errors[$val] = ts("Please select the Amount Statistics");
        }
      }
    }

    return $errors;
  }

  function from() {
    $softCreditJoin = "LEFT";
    if (CRM_Utils_Array::value('soft_amount', $this->_params['fields']) &&
      !CRM_Utils_Array::value('total_amount', $this->_params['fields'])) {
      // if its only soft credit stats, use inner join
      $softCreditJoin = "INNER";
    }

    $this->_from = "
        FROM civicrm_contact  {$this->_aliases['civicrm_contact']}
             INNER JOIN civicrm_contribution   {$this->_aliases['civicrm_contribution']}
                     ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_contribution']}.contact_id AND
                        {$this->_aliases['civicrm_contribution']}.is_test = 0
             {$softCreditJoin} JOIN civicrm_contribution_soft {$this->_aliases['civicrm_contribution_soft']}
                       ON {$this->_aliases['civicrm_contribution_soft']}.contribution_id = {$this->_aliases['civicrm_contribution']}.id
             LEFT  JOIN civicrm_financial_type  {$this->_aliases['civicrm_financial_type']}
                     ON {$this->_aliases['civicrm_contribution']}.financial_type_id ={$this->_aliases['civicrm_financial_type']}.id
             LEFT  JOIN civicrm_email {$this->_aliases['civicrm_email']}
                     ON ({$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_email']}.contact_id AND
                        {$this->_aliases['civicrm_email']}.is_primary = 1)

             LEFT  JOIN civicrm_phone {$this->_aliases['civicrm_phone']}
                     ON ({$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_phone']}.contact_id AND
                        {$this->_aliases['civicrm_phone']}.is_primary = 1)";

    if ($this->_addressField) {
      $this->_from .= "
                  LEFT JOIN civicrm_address {$this->_aliases['civicrm_address']}
                         ON {$this->_aliases['civicrm_contact']}.id =
                            {$this->_aliases['civicrm_address']}.contact_id AND
                            {$this->_aliases['civicrm_address']}.is_primary = 1\n";
    }
    
    /*
     * BOSW1508247 vnv.nl - exporteren data clubcollect
     * Add country name or the country id will show in the Preview Excel 
     */
    $this->_from .= "
      LEFT JOIN civicrm_country country_civireport
        ON country_civireport.id = address_civireport.country_id";
    
    /*
     * BOSW1508247 vnv.nl - exporteren data clubcollect
     * Create a left join to civicrm membership, for the custom fields Lidmaatschap- Maatschappij
     * 
     * The memebership is only active if it is between start date and end date, and if status id is 9 whatever is_override is
     */
    $this->_from .= "
      LEFT JOIN civicrm_membership  membership_civireport 
        ON membership_civireport.contact_id = contact_civireport.id AND
            (membership_civireport.start_date <= '" . date('Y-m-d') . "' AND (membership_civireport.end_date >= '" . date('Y-m-d') . "' OR membership_civireport.end_date IS NULL)) AND
            membership_civireport.status_id = '9'";
    
    // membership type to name
    $this->_from .= "
      LEFT JOIN civicrm_membership_type membership_type_civireport
        ON membership_type_civireport.id = membership_civireport.membership_type_id";
    
  }
  
  function groupBy() {
    $this->_groupBy = "";
    $append = FALSE;
    if (is_array($this->_params['group_bys']) &&
      !empty($this->_params['group_bys'])
    ) {
      foreach ($this->_columns as $tableName => $table) {
        if (array_key_exists('group_bys', $table)) {
          foreach ($table['group_bys'] as $fieldName => $field) {
            if (CRM_Utils_Array::value($fieldName, $this->_params['group_bys'])) {
              if (CRM_Utils_Array::value('chart', $field)) {
                $this->assign('chartSupported', TRUE);
              }

              if (CRM_Utils_Array::value('frequency', $table['group_bys'][$fieldName]) &&
                CRM_Utils_Array::value($fieldName, $this->_params['group_bys_freq'])
              ) {

                $append = "YEAR({$field['dbAlias']}),";
                if (in_array(strtolower($this->_params['group_bys_freq'][$fieldName]),
                    array('year')
                  )) {
                  $append = '';
                }
                $this->_groupBy[] = "$append {$this->_params['group_bys_freq'][$fieldName]}({$field['dbAlias']})";
                $append = TRUE;
              }
              else {
                $this->_groupBy[] = $field['dbAlias'];
              }
            }
          }
        }
      }

      if (!empty($this->_statFields) &&
        (($append && count($this->_groupBy) <= 1) || (!$append)) && !$this->_having
      ) {
        $this->_rollup = " WITH ROLLUP";
      }
      $this->_groupBy = "GROUP BY " . implode(', ', $this->_groupBy) . " {$this->_rollup} ";
    }
    else {
      $this->_groupBy = "GROUP BY {$this->_aliases['civicrm_contact']}.id";
    }
  }

  function statistics(&$rows) {
    $statistics = parent::statistics($rows);

    $softCredit = CRM_Utils_Array::value('soft_amount', $this->_params['fields']);
    $onlySoftCredit = $softCredit && !CRM_Utils_Array::value('total_amount', $this->_params['fields']);

    $select = "SELECT
COUNT({$this->_aliases['civicrm_contribution']}.total_amount )        as civicrm_contribution_total_amount_count,
SUM({$this->_aliases['civicrm_contribution']}.total_amount )          as civicrm_contribution_total_amount_sum,
ROUND(AVG({$this->_aliases['civicrm_contribution']}.total_amount), 2) as civicrm_contribution_total_amount_avg,
{$this->_aliases['civicrm_contribution']}.currency                    as currency";

    if ($softCredit) {
      $select .= ",
COUNT({$this->_aliases['civicrm_contribution_soft']}.amount )        as civicrm_contribution_soft_soft_amount_count,
SUM({$this->_aliases['civicrm_contribution_soft']}.amount )          as civicrm_contribution_soft_soft_amount_sum,
ROUND(AVG({$this->_aliases['civicrm_contribution_soft']}.amount), 2) as civicrm_contribution_soft_soft_amount_avg";
    }
    $group = "\nGROUP BY {$this->_aliases['civicrm_contribution']}.currency";
    $sql = "{$select} {$this->_from} {$this->_where} {$group} {$this->_having}";

    $dao = CRM_Core_DAO::executeQuery($sql);
    $totalAmount = $average = $softTotalAmount = $softAverage = array();
    $count = $softCount = 0;
    while ($dao->fetch()) {
      $totalAmount[] = CRM_Utils_Money::format($dao->civicrm_contribution_total_amount_sum, $dao->currency)." (".$dao->civicrm_contribution_total_amount_count.")";
      $average[] = CRM_Utils_Money::format($dao->civicrm_contribution_total_amount_avg, $dao->currency);
      $count += $dao->civicrm_contribution_total_amount_count;

      if ($softCredit) {
        $softTotalAmount[] = CRM_Utils_Money::format($dao->civicrm_contribution_soft_soft_amount_sum, $dao->currency)." (".$dao->civicrm_contribution_soft_soft_amount_count.")";
        $softAverage[] = CRM_Utils_Money::format($dao->civicrm_contribution_soft_soft_amount_avg, $dao->currency);
        $softCount += $dao->civicrm_contribution_soft_soft_amount_count;
      }
    }

    if (!$onlySoftCredit) {
      $statistics['counts']['amount'] = array(
        'title' => ts('Total Amount'),
        'value' => implode(',  ', $totalAmount),
        'type' => CRM_Utils_Type::T_STRING,
      );
      $statistics['counts']['count'] = array(
        'title' => ts('Total Donations'),
        'value' => $count,
      );
      $statistics['counts']['avg'] = array(
        'title' => ts('Average'),
        'value' => implode(',  ', $average),
        'type' => CRM_Utils_Type::T_STRING,
      );
    }
    if ($softCredit) {
      $statistics['counts']['soft_amount'] = array(
        'title' => ts('Total Soft Credit Amount'),
        'value' => implode(',  ', $softTotalAmount),
        'type' => CRM_Utils_Type::T_STRING,
      );
      $statistics['counts']['soft_count'] = array(
        'title' => ts('Total Soft Credits'),
        'value' => $softCount,
      );
      $statistics['counts']['soft_avg'] = array(
        'title' => ts('Average Soft Credit'),
        'value' => implode(',  ', $softAverage),
        'type' => CRM_Utils_Type::T_STRING,
      );
    }
    return $statistics;
  }

  function postProcess() {
    $this->buildACLClause($this->_aliases['civicrm_contact']);    
    parent::postProcess();
  }

  function buildChart(&$rows) {
    $graphRows = array();

    if (CRM_Utils_Array::value('charts', $this->_params)) {
      if (CRM_Utils_Array::value('receive_date', $this->_params['group_bys'])) {

        $contrib = CRM_Utils_Array::value('total_amount', $this->_params['fields']) ? TRUE : FALSE;
        $softContrib = CRM_Utils_Array::value('soft_amount', $this->_params['fields']) ? TRUE : FALSE;

        foreach ($rows as $key => $row) {
          if ($row['civicrm_contribution_receive_date_subtotal']) {
            $graphRows['receive_date'][] = $row['civicrm_contribution_receive_date_start'];
            $graphRows[$this->_interval][] = $row['civicrm_contribution_receive_date_interval'];
            if ($softContrib && $contrib) {
              // both contri & soft contri stats are present
              $graphRows['multiValue'][0][] = $row['civicrm_contribution_total_amount_sum'];
              $graphRows['multiValue'][1][] = $row['civicrm_contribution_soft_soft_amount_sum'];
            } else if ($softContrib) {
              // only soft contributions
              $graphRows['multiValue'][0][] = $row['civicrm_contribution_soft_soft_amount_sum'];
            } else {
              // only contributions
              $graphRows['multiValue'][0][] = $row['civicrm_contribution_total_amount_sum'];
            }
          }
        }

        if ($softContrib && $contrib) {
          $graphRows['barKeys'][0] = ts('Contributions');
          $graphRows['barKeys'][1] = ts('Soft Credits');
          $graphRows['legend'] = ts('Contributions and Soft Credits');
        } else if ($softContrib) {
          $graphRows['legend'] = ts('Soft Credits');
        }

        // build the chart.
        $config             = CRM_Core_Config::Singleton();
        $graphRows['xname'] = $this->_interval;
        $graphRows['yname'] = "Amount ({$config->defaultCurrency})";
        CRM_Utils_OpenFlashChart::chart($graphRows, $this->_params['charts'], $this->_interval);
        $this->assign('chartType', $this->_params['charts']);
      }
    }
  }

  function alterDisplay(&$rows) {
    // custom code to alter rows
    $entryFound = FALSE;
    
    foreach ($rows as $rowNum => $row) {
      // make count columns point to detail report
      if (CRM_Utils_Array::value('receive_date', $this->_params['group_bys']) &&
        CRM_Utils_Array::value('civicrm_contribution_receive_date_start', $row) &&
        CRM_Utils_Array::value('civicrm_contribution_receive_date_start', $row) &&
        CRM_Utils_Array::value('civicrm_contribution_receive_date_subtotal', $row)
      ) {

        $dateStart = CRM_Utils_Date::customFormat($row['civicrm_contribution_receive_date_start'], '%Y%m%d');
        $endDate   = new DateTime($dateStart);
        $dateEnd   = array();

        list($dateEnd['Y'], $dateEnd['M'], $dateEnd['d']) = explode(':', $endDate->format('Y:m:d'));

        switch (strtolower($this->_params['group_bys_freq']['receive_date'])) {
          case 'month':
            $dateEnd = date("Ymd", mktime(0, 0, 0, $dateEnd['M'] + 1,
                $dateEnd['d'] - 1, $dateEnd['Y']
              ));
            break;

          case 'year':
            $dateEnd = date("Ymd", mktime(0, 0, 0, $dateEnd['M'],
                $dateEnd['d'] - 1, $dateEnd['Y'] + 1
              ));
            break;

          case 'yearweek':
            $dateEnd = date("Ymd", mktime(0, 0, 0, $dateEnd['M'],
                $dateEnd['d'] + 6, $dateEnd['Y']
              ));
            break;

          case 'quarter':
            $dateEnd = date("Ymd", mktime(0, 0, 0, $dateEnd['M'] + 3,
                $dateEnd['d'] - 1, $dateEnd['Y']
              ));
            break;
        }
        $url = CRM_Report_Utils_Report::getNextUrl('contribute/detail',
                                                   "reset=1&force=1&receive_date_from={$dateStart}&receive_date_to={$dateEnd}",
                                                   $this->_absoluteUrl,
                                                   $this->_id,
                                                   $this->_drilldownReport
                                                   );
        $rows[$rowNum]['civicrm_contribution_receive_date_start_link'] = $url;
        $rows[$rowNum]['civicrm_contribution_receive_date_start_hover'] = ts('List all contribution(s) for this date unit.');
        $entryFound = TRUE;
      }

      // make subtotals look nicer
      if (array_key_exists('civicrm_contribution_receive_date_subtotal', $row) &&
        !$row['civicrm_contribution_receive_date_subtotal']
      ) {
        $this->fixSubTotalDisplay($rows[$rowNum], $this->_statFields);
        $entryFound = TRUE;
      }

      // convert display name to links
      if (array_key_exists('civicrm_contact_sort_name', $row) &&
        array_key_exists('civicrm_contact_id', $row)
      ) {
        $url = CRM_Report_Utils_Report::getNextUrl('contribute/detail',
          'reset=1&force=1&id_op=eq&id_value=' . $row['civicrm_contact_id'],
          $this->_absoluteUrl, $this->_id, $this->_drilldownReport
        );
        $rows[$rowNum]['civicrm_contact_sort_name_link'] = $url;
        $rows[$rowNum]['civicrm_contact_sort_name_hover'] = ts("Lists detailed contribution(s) for this record.");
        $entryFound = TRUE;
      }

      // If using campaigns, convert campaign_id to campaign title
      if (array_key_exists('civicrm_contribution_campaign_id', $row)) {
        if ($value = $row['civicrm_contribution_campaign_id']) {
          $rows[$rowNum]['civicrm_contribution_campaign_id'] = $this->activeCampaigns[$value];
        }
        $entryFound = TRUE;
      }

      $entryFound = $this->alterDisplayAddressFields($row, $rows, $rowNum, 'contribute/detail', 'List all contribution(s) for this ') ? TRUE : $entryFound;

      /*
       * BOSW1508247 vnv.nl - exporteren data clubcollect
       * Translate country
       */
      if (array_key_exists('civicrm_country_civicrm_country_name', $row)) {
        if ($value = $row['civicrm_country_civicrm_country_name']) {
          $rows[$rowNum]['civicrm_country_civicrm_country_name'] = ts($value, array('context' => 'country'));
        }
      }
      
      // skip looking further in rows, if first row itself doesn't
      // have the column we need
      if (!$entryFound) {
        break;
      }
    }
  }
}

