<?php
require_once "class/class.users.php";

$u = new users();
if (!$user = $u->userEntered()){
	header("Location: auth.php");
}
session_start();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="../libs/jqgrid/css/jquery-ui-1.10.3.custom.css">
		<link rel="stylesheet" type="text/css" media="screen" href="../libs/bootstrap/css/bootstrap.css" />
		<link rel="stylesheet" href='../css/index.css'/>
		<link rel="stylesheet" type="text/css" media="screen" href="../libs/jqgrid/css/ui.jqgrid.css" />
		<title>Укрдомик панель управления</title>
		<script src="../libs/jquery-2.0.3.js"></script>
		<script src="../libs/bootstrap/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="../libs/jqgrid/js/jquery-ui-1.10.3.custom.min.js"></script>
		<script type="text/javascript" src="../libs/jqgrid/js/jquery.ui.datepicker-ru.js"></script>
		<script src="../libs/jqgrid/js/i18n/grid.locale-ru.js"></script>
		<script src="../libs/jqgrid/js/jquery.jqGrid.min.js"></script>
		<script src="../libs/angular.min.js"></script>
		<script src="../libs/jquery.maskedinput.js"></script>
		<script src="../js/index.js"></script>
	</head>
	<body>
    	<div ng-app="tasks" id="page">
    		<div id="user_info">
				<?php echo "Пользователь - ".$user; ?>
                <input type="hidden" id="user_name" value="<?php echo $user; ?>">
				<button class="btn btn-danger btn-my" id="close">Выход</button>
			</div>
    		<ul class="nav nav-tabs">
    			<li class="active secondary-nav"><a href="#calls" data-toggle="tab">Звонки</a></li>
    			<li class="secondary-nav"><a href="#orders" data-toggle="tab">Заказы</a></li>
    			<li class="secondary-nav"><a href="#returns" data-toggle="tab">Возвраты</a></li>
                <?php if ($_SESSION['tasks']['role'] == 'admin') { ?>
                <li class="secondary-nav"><a href="#users" data-toggle="tab">Пользователи</a></li>
                <?php } ?>
    		</ul>
    		<div class="tab-content" ng-controller="app" data-ng-init="init()">
				<div class="tab-pane active" id="calls" >
					<p align="right">
						<button class="btn btn-info" ng-click="add_call()"><i class="icon-plus"></i>  
							Добавить звонок
						</button>
						<button class="btn btn-info" ng-click="edit_call()"><i class="icon-pencil"></i>  
							Редактировать звонок
						</button>
					</p>
					<table id="calls_tab"></table>
					<div id="calls_tab_pager"></div>
				</div>
				<div class="tab-pane" id="orders">
					<p align="right">
						<button class="btn btn-info" ng-click="order_detail()"><i class="icon-list-alt"></i>  
							Детально
						</button>
						<button class="btn btn-info" ng-click="add_order()"><i class="icon-plus"></i>  
							Добавить заказ
						</button>
						<button class="btn btn-info" ng-click="edit_order()"><i class="icon-pencil"></i>  
							Редактировать заказ
						</button>
                        <?php if ($_SESSION['tasks']['role'] == 'admin') { ?>
                            <button class="btn btn-info" ng-click="del_order()"><i class="icon-trash"></i>  
                                Удалить заказ
                            </button>
                        <?php } ?>
					</p>
					<table id="orders_tab"></table>
					<div id="orders_tab_pager"></div>
				</div>
				<div class="tab-pane" id="returns">
					<p align="right">
						<button class="btn btn-info" ng-click="add_return()"><i class="icon-plus"></i>  
							Добавить возврат
						</button>
						<button class="btn btn-info" ng-click="del_return()"><i class="icon-remove"></i>  
							Удалить возврат
						</button>
					</p>
					<table id="returns_tab"></table>
					<div id="returns_tab_pager"></div>
				</div>
                <?php if ($_SESSION['tasks']['role'] == 'admin') { ?>
                <div class="tab-pane" id="users">
                    <p align="right">
                        <button class="btn btn-info" ng-click="add_user()"><i class="icon-plus"></i>  
                            Добавить пользователя
                        </button>
                        <button class="btn btn-info" ng-click="edit_user()"><i class="icon-pencil"></i>  
                            Редактировать пользователя
                        </button>
                        <button class="btn btn-info" ng-click="del_user()"><i class="icon-trash"></i>  
                            Удалить пользователя
                        </button>
                    </p>
                    <table id="users_tab"></table>
                    <div id="users_tab_pager"></div>
                </div>
                <?php } ?>
			</div>
    	</div>
    	<div id="order_detail_form" class="modal hide fade">
    		<div class="modal-header">
    			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    			<h3>Детали заказа № <span id="order_number"></span></h3>
    		</div>
    		<div class="modal-body">
    		<table width="100%">
    			<tr>
    				<td>Код клиента</td>
    				<td><span id="order_client_code"></span></td>
    			</tr>
    			<tr>
    				<td>Телефон клиента</td>
    				<td><span id="order_phones"></span></td>
    			</tr>
    			<tr>
    				<td>Имя клиента</td>
    				<td><span id="order_name"></span></td>
    			</tr>
    			<tr>
    				<td>Комментарии</td>
    				<td><div id="order_comments"></div></td>
    			</tr>
    			<tr>
    				<td>Количество товара</td>
    				<td><span id="order_count"></span></td>
    			</tr>
    			<tr>
    				<td>Дата звонка</td>
    				<td><span id="order_dtcall"></span></td>
    			</tr>
    			<tr>
    				<td>Дата ввода звонка</td>
    				<td><span id="order_dtenter"></span></td>
    			</tr>
    			<tr>
    				<td>Стоимость доставки</td>
    				<td><span id="order_pricedelivery"></span></td>
    			</tr>
    			<tr>
    				<td>Предоплата</td>
    				<td><span id="order_prepayment"></span></td>
    			</tr>
    			<tr>
    				<td>Сумма всего</td>
    				<td><span id="order_sum"></span></td>
    			</tr>
    			<tr>
    				<td>Сумма всего к оплате</td>
    				<td><span id="order_sumall"></span></td>
    			</tr>
    			<tr>
    				<td>Доставка</td>
    				<td><span id="order_delivery"></span></td>
    			</tr>
    			<tr>
    				<td style="padding-left:15px">Город</td>
    				<td><span id="order_delcity"></span></td>
    			</tr>
    			<tr>
    				<td style="padding-left:15px">Улица</td>
    				<td><span id="order_delstreet"></span></td>
    			</tr>
    			<tr>
    				<td style="padding-left:15px">Дом</td>
    				<td><span id="order_delhouse"></span></td>
    			</tr>
    			<tr>
    				<td style="padding-left:15px">Квартира</td>
    				<td><span id="order_delapartment"></span></td>
    			</tr>
    			<tr>
    				<td style="padding-left:15px">Желаемое время доставки</td>
    				<td><span id="order_time"></span></td>
    			</tr>
    			<tr>
    				<td style="padding-left:15px">Кто доставляет</td>
    				<td><span id="order_deliveryman"></span></td>
    			</tr>
    			<tr>
    				<td style="padding-left:15px">Название перевозчика</td>
    				<td><span id="order_deliveryman_name"></span></td>
    			</tr>
    			<tr>
    				<td style="padding-left:15px">Склад перевозчика</td>
    				<td><span id="order_deliveryman_werehouse"></span></td>
    			</tr>
    			<tr>
    				<td style="padding-left:15px">Обратная доставка</td>
    				<td><span id="order_redelivery"></span></td>
    			</tr>
    			<tr>
    				<td style="padding-left:15px">Склад обратной доставки</td>
    				<td><span id="order_redelivery_werehousefrom"></span></td>
    			</tr>
    			<tr>
    				<td style="padding-left:15px">Ориентировочное прибытие</td>
    				<td><span id="order_redeliverydt"></span></td>
    			</tr>
    			<tr>
    				<td>Статус заказа</td>
    				<td><span id="order_status"></span></td>
    			</tr>
    		</table>
    		</div>
    		<div class="modal-footer">
    				
			</div>
    		</div>
    	</div>
	</body>
</html>