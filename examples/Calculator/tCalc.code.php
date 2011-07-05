<?php
import( 'system.web.ui.page.*' );

import( 'system.web.ui.Button' );
import( 'system.web.ui.form.DropDownList' );
import( 'system.web.ui.form.OptionItem' );
import( 'system.web.ui.Label' );
import( 'system.web.ui.form.TextBox' );
import( 'system.web.ui.Icon' );

	class tCalc extends Page
	{
		public $txtNum1;
		public $txtNum2;
		public $txtResult;
		public $cmbSom;
		public $cmbSub;
		public $cmbMul;
		public $cmbDiv;
		public $txtComm;

		private $logger;

		public function init()
		{
			//$this->logger->debug("Page_Init()");

			$eh = new EventHandler ( array($this,'calcular'));

			//$this->logger->debug("EventHandler created");

			$this->cmbDiv->OnClick = $eh;
			$this->cmbMul->OnClick = $eh;
			$this->cmbSom->OnClick = $eh;
			$this->cmbSub->OnClick = $eh;
		}

		public function load()
		{
			//$this->logger = new MyLog("tCalc.txt");
		}

		public function calcular(Component $sender, $args)
		{
			//$this->logger->debug("calcular() Called",__FILE__,__LINE__);

			if ($sender == $this->cmbSom)
			{
				$result = $this->txtNum1->Value + $this->txtNum2->Value;
				$this->txtResult->Value = $result;
			}

			if ($sender == $this->cmbMul)
			{
				$result = $this->txtNum1->Value * $this->txtNum2->Value;
				$this->txtResult->Value = $result;
			}

			if ($sender == $this->cmbSub)
			{
				$result = $this->txtNum1->Value - $this->txtNum2->Value;
				$this->txtResult->Value = $result;
			}

			if ($sender == $this->cmbDiv)
			{
				if ($this->txtNum2->Value == 0)
				{
					$this->txtResult->Value = 'Erro!';
				}
				else
				{
					$result = $this->txtNum1->Value / $this->txtNum2->Value;
					$this->txtResult->Value = $result;
				}
			}
		}
	}
?>