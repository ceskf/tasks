var tasks = angular.module('tasks',[]);

tasks.controller('app', ['$scope','$http','$timeout', function($scope,$http,$timeout){
    $scope.onTimeout = function(){
        $scope.http();
    }
    $scope.timeout = $timeout($scope.onTimeout,60000);
    $scope.params = '';
    $scope.local_returns = '';
    $scope.user_name = '';
	$scope.http = function(){
        $http({
        	method: 'GET',
        	url: 'ajax/users.php?type=checks'
    	})
    	.success(function (data, status, headers, config) {
    		if (data == '0') document.location.href = 'auth.php?exit=true';
        	$scope.timeout = $timeout($scope.onTimeout,60000);
    	})
    	.error(function (data, status, headers, config) {
    		document.location.href = 'auth.php?exit=true';
    	});
    }
    $http({
     	method: 'GET',
       	url: 'ajax/params.php'
    })
    .success(function (data, status, headers, config) {
    	$scope.params = data;
    	$scope.orders();
    	$scope.returns();
        $scope.users();
    })
    .error(function (data, status, headers, config) {
    	/*document.location.href = 'auth.php?exit=true';*/
    });
    $scope.init = function(){
    	$scope.calls();
        $scope.user_name = $("#user_name").val();
    }
    $scope.products = {
    	'':'',
        'wallpapers':'Обои',
        'laminat':'Ламинат',
        'kovrolin':'Ковролин',
        'parket':'Паркет'
    }; 
    $scope.user_roles = {
        '':'',
        'admin':'Администратор',
        'seller':'Продавец',
        'manager':'Менеджер'
    };
    $scope.status = {
    	'':'',
    	'Не обработан':'Не обработан',
    	'Собирается':'Собирается',
    	'Доставляется':'Доставляется',
    	'Отправлен в другой город':'Отправлен в другой город',
    	'Выполнен':'Выполнен'
    }
    $scope.calls = function(){
    	$("#calls_tab").jqGrid({ 
    		url:'ajax/calls.php?q=1', 
    		datatype: "json", 
    		height: '100%',
    		mtype: 'POST', 
    		colNames:['№ звонка','Телефон','Имя','Комментарии','Кол-во товара','Дата звонка','Дата звонка','Время звонка','Дата ввода','Пользователь'], 
    		colModel:[ 
    			{name:'id',index:'id', width:50, align:"right"}, 
    			{name:'phone',index:'phone', width:125, align:"right",editable:true,editrules: {required:true}}, 
    			{name:'name',index:'name', width:150, align:"right",editable:true,editrules: {required:true}}, 
    			{name:'comment',index:'comment', width:350, align:"left",editable:true, edittype:'textarea'}, 
    			{name:'count',index:'count', width:100, align:"right", sortable:false},
    			{name:'dt_call',index:'dt_call', width:150, align:"right",editable:false}, 
				{name:'dt_call_dt', index:'dt_call_dt', editable:true, editrules: {edithidden:true,required:true},hidden:true, editoptions: {dataInit: initDateEdit}	},
				{name:'dt_call_time', index:'dt_call_time', editable:true, editrules: {edithidden:true,required:true, custom:true, custom_func:function(value, column){
						val = value.split(":");
						if (val[0] < 25 && val[1] < 59) 
							return[true,''];
						else
							return [false,column+": Неверный формат времени"];
					}},hidden:true, editoptions: {dataInit: function(elem){
						$(elem).mask("99:99");
					}}
				},
    			{name:'dt_enter',index:'dt_enter', width:150, align:"right"},
    			{name:'user',index:'user', width:100, align:"right"}
    		], 
    		rowNum:20, 
    		rowList:[20,50,100,150], 
    		pager: '#calls_tab_pager', 
    		sortname: 'dt_enter', 
    		viewrecords: true, 
    		sortorder: "desc", 
    		multiselect: false, 
    		subGrid: true, 
    		width: 1175,
    		caption: "Звонки", 
    		editurl : 'ajax/calls.php?q=1',
    		subGridOptions: {
    			"plusicon" : "ui-icon-triangle-1-e", 
    			"minusicon" : "ui-icon-triangle-1-s", 
    			"openicon" : "ui-icon-arrowreturn-1-e" 
    		}, 
    		subGridRowExpanded: function(subgrid_id, row_id) { 
    			var subgrid_table_id, pager_id; 
    			subgrid_table_id = subgrid_id+"_t"; 
    			pager_id = "p_"+subgrid_table_id; 
    			$("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>"); 
    			$("#"+subgrid_table_id).jqGrid({ 
    				url:"ajax/calls.php?q=2&id="+row_id, 
    				datatype: "json", 
    				mtype: 'POST', 
    				colNames: ['ID','TYE_ID','Товар','Производитель','Коллекция','Артикул','Кол-во','Озвученная цена'], 
    				colModel: [ 
    					{name:"id",index:"id",width:100, hidden:true}, 
    					{name:"type_id",index:"type_id",width:100, hidden:true}, 
    					{
    						name:"product",
    						index:"product",
    						width:100,
    						align:"right",
    						editable:true,
    						edittype: 'select',
    						editrules: {required:true},
    						editoptions:{value:$scope.products, dataEvents: [
                    			{
                    				type: 'change',
                    				fn: function(){
                        				$("#tr_manufacturer select").empty();
                        				$("#tr_collection select").empty();
                        				var val = $(this).val();
                        				if (val != ''){
                        					$("#tr_manufacturer select").append("<option value=''></option>");
                            				var data = $scope.params[val]['manufacturers'];
                            				for (var i in data){
                                				$("#tr_manufacturer select").append('<option value="'+i+'">'+data[i]+'</option>');
                            				}
                        				}
                    				}
                				}        
                			]}
    					}, 
    					{
    						name:"manufacturer",
    						index:"manufacturer",
    						width:150,
    						align:"right",
    						editable:true, 
    						edittype:"select",
    						editrules: {required:true},
    						editoptions:{value:"'':", dataEvents: [
                    			{
                    				type: 'change',
                    				fn: function(){
                        				$("#tr_collection select").empty();
                        				var val = $(this).val();
                        				var type =  $("#tr_product select").val();
                        				if (val != ''){
                            				var data = $scope.params[type]['catalogs'][val];
                            				$("#tr_collection select").append("<option value=''></option>");
                            				for (var i in data){
                                				$("#tr_collection select").append('<option value="'+i+'">'+data[i]+'</option>');
                            				}
                        				}
                    				}
                				}        
                			]}
    					}, 
    					{name:"collection",index:"collection",width:150,align:"right",editable:true, edittype:"select", editoptions:{value:"'':"},editrules: {required:true}}, 
    					{name:"articul",index:"articul",width:100,align:"right",editable:true, editrules: {required:true}}, 
    					{name:"count",index:"count",width:100,align:"right",editable:true},
    					{name:"price",index:"price",width:100,align:"right",editable:true},
    				], 
    				rowNum:20, 
    				pager: pager_id, 
    				sortname: 'count', 
    				sortorder: "asc", 
    				height: '100%',
    				loadComplete: function(data){
    					if (data.records != 0)
    						$("tr#"+row_id+" td[aria-describedby=calls_tab_count]").text(data.rows.length);
    				},
    				viewrecords: true,
    				editurl : 'ajax/calls.php?q=2&id='+row_id,
    			}); 
    			$("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{
                    edit:false,
                    add:true,
                    del:true,
                    search:false
                },
                {
                    width:400, 
                    recreateForm: true, 
                    reloadAfterSubmit: true, 
                    closeAfterEdit:true
                },
                {
                    width:400, 
                    recreateForm: false, 
                    reloadAfterSubmit: true, 
                    closeAfterAdd:true,
                    beforeInitData: function(){
                        var data =  $("#calls_tab").jqGrid('getRowData',row_id);
                        if (data.user != $scope.user_name){
                            alert("Вы не можете редактировать звонки продавцов");
                            return false;
                        }
                        else 
                            return true;
                    }
                },
                {
                    beforeInitData: function(){
                        var data =  $("#calls_tab").jqGrid('getRowData',row_id);
                        if (data.user != $scope.user_name){
                            alert("Вы не можете редактировать звонки продавцов");
                            return false;
                        }
                        else 
                            return true;
                    }
                }) 
    		} 
    	}); 
		$("#calls_tab").jqGrid('navGrid','#calls_tab_pager',{add:false,edit:false,del:false,search:false});
    };

    $scope.orders = function(){
    	$("#orders_tab").jqGrid({ 
    		url:'ajax/orders.php?q=1', 
    		datatype: "json", 
    		height: '100%',
    		mtype: 'POST', 
    		colNames:['№ заказа',
    				  'Код клиента',
    				  'Телефон клиента',
    				  'Имя',
    				  'Комментарии',
    				  'Кол-во товара',
    				  'Дата звонка',
    				  'Дата звонка',
    				  'Время звонка',
    				  'Дата ввода',
    				  'Стоимость доставки',
    				  'Предоплата',
    				  'Сумма заказа',
    				  'Сумма к оплате',
    				  'Доставка',
    				  'Город',
    				  'Улица',
    				  '№ дома',
    				  '№ квартиры',
    				  'Желаемое время доставки',
    				  'Кто доставляет',
    				  'Название перевозчика',
    				  '№ склада перевозчика',
    				  'ОД',
    				  '№ склада отправки ОД',
    				  'Дата прибытия ОД',
    				  'Статус',
    				  'Пользователь'
    		], 
    		colModel:[ 
    			{name:'id',index:'id', width:50, align:"right"}, 
    			{name:'client_code',index:'client_code', width:100, align:"right",editable:true, edittype:"select", editoptions:{value:$scope.params.codes}}, 
    			{name:'phones',index:'phones', width:125, align:"right",editable:true,editrules: {required:true}}, 
    			{name:'name',index:'name', width:150, align:"right",editable:true,editrules: {required:true}}, 
    			{name:'comments',index:'comments', width:350, align:"left",editable:true, edittype:'textarea', hidden:true,editrules: {edithidden:true}}, 
    			{name:'count',index:'count', width:100, align:"right", sortable:false},
    			{name:'dt_call',index:'dt_call', width:150, align:"right",editable:false}, 
				{name:'dt_call_dt', index:'dt_call_dt', editable:true, editrules: {edithidden:true,required:true},hidden:true, editoptions: {dataInit: initDateEdit}},
				{name:'dt_call_time', index:'dt_call_time', editable:true, editrules: {edithidden:true,required:true, custom:true, custom_func:function(value, column){
						val = value.split(":");
						if (val[0] < 25 && val[1] < 59) 
							return[true,''];
						else
							return [false,column+": Неверный формат времени"];
					}},hidden:true, editoptions: {dataInit: function(elem){
						$(elem).mask("99:99");
					}}
				},
    			{name:'dt_enter',index:'dt_enter', width:150, align:"right"},
    			{name:'price_delivery',index:'price_delivery',width:100,align:"right", editable:true},
    			{name:'prepayment',index:'prepayment',width:100,align:"right", editable:true},
    			{name:'sum',index:'sum',width:100,align:"right"},
    			{name:'sum_all',index:'sum_all',width:100,align:"right"},
    			{name:'delivery',index:'delivery',width:50,align:"center",edittype:"checkbox",editoptions: {value:"Да:Нет", dataEvents: [
                    			{
                    				type: 'change',
                    				fn: function(){
                        				if ($(this).is(':checked') == true){
                        					$("#tr_del_city").show();
                        					$("#tr_del_street").show();
                        					$("#tr_del_house").show();
                        					$("#tr_del_apartment").show();
                        					$("#tr_time").show();
                        					$("#tr_deliveryman").show();
                        					$("#tr_deliveryman_name").show();
                        					$("#tr_deliveryman_werehouse").show();
                        					$("#tr_redelivery").show();
                        					$("#tr_redelivery_werehouse_from").show();
                        					$("#tr_redelivery_dt").show();
                        				}
                        				else{
                        					$("#tr_del_city").hide();
                        					$("#tr_del_street").hide();
                        					$("#tr_del_house").hide();
                        					$("#tr_del_apartment").hide();
                        					$("#tr_time").hide();
                        					$("#tr_deliveryman").hide();
                        					$("#tr_deliveryman_name").hide();
                        					$("#tr_deliveryman_werehouse").hide();
                        					$("#tr_redelivery").hide();
                        					$("#tr_redelivery_werehouse_from").hide();
                        					$("#tr_redelivery_dt").hide();
                        				}
                        				
                    				}
                				}        
                			]}, hidden:true,editrules: {edithidden:true}, editable:true},
    			{name:'del_city',index:'del_city',width:100,align:"right", hidden:true,editrules: {edithidden:true}, editable:true},
    			{name:'del_street',index:'del_street',width:100,align:"right", hidden:true,editrules: {edithidden:true}, editable:true},
    			{name:'del_house',index:'del_house',width:100,align:"right", hidden:true,editrules: {edithidden:true}, editable:true},
    			{name:'del_apartment',index:'del_apartment',width:100,align:"right", hidden:true,editrules: {edithidden:true}, editable:true},
    			{name:'time',index:'time',width:100,align:"right", editable:true, editrules: {edithidden:true, custom:true, custom_func:function(value, column){
						if (value != ''){
							val = value.split(":");
							if (val[0] < 25 && val[1] < 59) 
								return[true,''];
							else
								return [false,column+": Неверный формат времени"];
						}
						else
							return[true,''];
					}},hidden:true, editoptions: {dataInit: function(elem){
						$(elem).mask("99:99");
					}}},
    			{name:'deliveryman',index:'deliveryman',width:100,align:"right", hidden:true,editrules: {edithidden:true}, editable:true},
    			{name:'deliveryman_name',index:'deliveryman_name',width:100,align:"right", hidden:true,editrules: {edithidden:true}, editable:true},
    			{name:'deliveryman_werehouse',index:'deliveryman_werehouse',width:100,align:"right", hidden:true,editrules: {edithidden:true}, editable:true},
    			{name:'redelivery',index:'redelivery',width:100,align:"right", hidden:true,editrules: {edithidden:true}, editable:true},
    			{name:'redelivery_werehouse_from',index:'redelivery_werehouse_from',width:100,align:"right", hidden:true,editrules: {edithidden:true}, editable:true},
    			{name:'redelivery_dt',index:'redelivery_dt',width:100,align:"right", editable:true, hidden:true, editrules: {edithidden:true},editoptions: {dataInit: initDateEditFuture}, editable:true},
    			{name:'status',index:'status', width:100, align:"right",editable:true, edittype:"select", editoptions:{value:$scope.status},editrules: {required:true}},
    			{name:'user',index:'user', width:100, align:"right"}
    		], 
    		rowNum:20, 
    		rowList:[20,50,100,150], 
    		pager: '#orders_tab_pager', 
    		sortname: 'dt_enter', 
    		viewrecords: true, 
    		sortorder: "desc", 
    		multiselect: false, 
    		afterInsertRow: function(rowid,rowdata){
            	if ($.inArray(rowdata.id, $scope.params.orders) < 0)
                	$scope.params.orders.push(rowdata.id);
            },
    		subGrid: true, 
            loadComplete : function(data){
                for (var i in data.rows){
                    if (data.rows[i]['cell'][5] == 0){
                        $("#orders_tab tr#"+data.rows[i]['cell'][0]+" td").css('background-color','#FF8585');
                    }
                }
            },
    		width: 1175,
    		caption: "Заказы", 
    		editurl : 'ajax/orders.php?q=1',
    		subGridOptions: {
    			"plusicon" : "ui-icon-triangle-1-e", 
    			"minusicon" : "ui-icon-triangle-1-s", 
    			"openicon" : "ui-icon-arrowreturn-1-e" 
    		}, 
    		subGridRowExpanded: function(subgrid_id, row_id) { 
    			var subgrid_table_id, pager_id; 
    			subgrid_table_id = subgrid_id+"_t"; 
    			pager_id = "p_"+subgrid_table_id; 
    			$("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>"); 
    			$("#"+subgrid_table_id).jqGrid({ 
    				url:"ajax/orders.php?q=2&id="+row_id, 
    				datatype: "json", 
    				mtype: 'POST', 
    				colNames: ['ID','TYE_ID','Товар','Производитель','Коллекция','Артикул','Склад','Кол-во','Цена'], 
    				colModel: [ 
    					{name:"id",index:"id",width:100, hidden:true}, 
    					{name:"type_id",index:"type_id",width:100, hidden:true}, 
    					{
    						name:"product",
    						index:"product",
    						width:100,
    						align:"right",
    						editable:true,
    						edittype: 'select',
    						editrules: {required:true},
    						editoptions:{value:$scope.products, dataEvents: [
                    			{
                    				type: 'change',
                    				fn: function(){
                        				$("#tr_manufacturer select").empty();
                        				$("#tr_collection select").empty();
                        				var val = $(this).val();
                        				if (val != ''){
                        					$("#tr_manufacturer select").append("<option value=''></option>");
                            				var data = $scope.params[val]['manufacturers'];
                            				for (var i in data){
                                				$("#tr_manufacturer select").append('<option value="'+i+'">'+data[i]+'</option>');
                            				}
                            				$("#tr_werehouse select").append("<option value=''></option>");
                            				var data = $scope.params[val]['werehouses'];
                            				for (var i in data){
                                				$("#tr_werehouse select").append('<option value="'+i+'">'+data[i]+'</option>');
                            				}
                        				}
                    				}
                				}        
                			]}
    					}, 
    					{
    						name:"manufacturer",
    						index:"manufacturer",
    						width:150,
    						align:"right",
    						editable:true, 
    						edittype:"select",
    						editrules: {required:true},
    						editoptions:{value:"'':", dataEvents: [
                    			{
                    				type: 'change',
                    				fn: function(){
                        				$("#tr_collection select").empty();
                        				var val = $(this).val();
                        				var type =  $("#tr_product select").val();
                        				if (val != ''){
                            				var data = $scope.params[type]['catalogs'][val];
                            				$("#tr_collection select").append("<option value=''></option>");
                            				for (var i in data){
                                				$("#tr_collection select").append('<option value="'+i+'">'+data[i]+'</option>');
                            				}
                        				}
                    				}
                				}        
                			]}
    					}, 
    					{name:"collection",index:"collection",width:150,align:"right",editable:true, edittype:"select", editoptions:{value:"'':"},editrules: {required:true}}, 
    					{name:"articul",index:"articul",width:100,align:"right",editable:true, editrules: {required:true}}, 
    					{name:"werehouse",index:"werehouse",width:100,align:"right",editable:true, editrules: {required:true}, edittype:'select', editoptions:{value:"'':"}}, 
    					{name:"count",index:"count",width:100,align:"right",editable:true},
    					{name:"price",index:"price",width:100,align:"right",editable:true},
    				], 
    				rowNum:20, 
    				pager: pager_id, 
    				sortname: 'count', 
    				loadComplete: function(data){
    					if (data.records != 0){
    						var sum = 0;
    						var col = 0;
    						for (var i in data.rows){
    							sum += parseFloat(data.rows[i]['cell'][8])*parseInt(data.rows[i]['cell'][7]);
    							col++;
    						}
    						$("tr#"+row_id+" td[aria-describedby=orders_tab_sum]").text(number_format(sum,2,'.','`'));
    						$("tr#"+row_id+" td[aria-describedby=orders_tab_count]").text(col);

    						var datam = $("#orders_tab").jqGrid('getRowData',row_id);
    						var sum_all = (sum+parseFloat(datam.price_delivery))-parseFloat(datam.prepayment);

                            if (col == 0){
                                $("#orders_tab tr#"+row_id+" td").css('background-color','#FF8585');
                            }
                            else
                                $("#orders_tab tr#"+row_id+" td").css('background-color','white');
    					
    						$("tr#"+row_id+" td[aria-describedby=orders_tab_sum_all]").text(number_format(sum_all,2,'.','`'));
    					}
    				},
    				sortorder: "asc", 
    				height: '100%',
    				viewrecords: true,
    				editurl : 'ajax/orders.php?q=2&id='+row_id,
    			}); 
    			$("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{
                    edit:true,
                    add:true,
                    del:true,
                    search:false
                },
                {width:400, 
                    recreateForm: true, 
                    reloadAfterSubmit: true, 
                    closeAfterEdit:true,
                    beforeInitData: function(){
                        var data =  $("#orders_tab").jqGrid('getRowData',row_id);
                        if (data.user != $scope.user_name){
                            alert("Вы не можете редактировать звонки продавцов");
                            return false;
                        }
                        else 
                            return true;
                    }, 
                    onInitializeForm: function(formid){
                    var gr = $("#"+subgrid_table_id).jqGrid('getGridParam', 'selrow');
                    var s_man = $("#"+subgrid_table_id).jqGrid('getCell', gr, 'manufacturer');
                    var s_cat = $("#"+subgrid_table_id).jqGrid('getCell', gr, 'collection');
                    var s_wh = $("#"+subgrid_table_id).jqGrid('getCell', gr, 'werehouse');
                    var type = $("select#product").val();
                    var mans = $scope.params[type]['manufacturers'];
                    for (man in mans){
                        if (man == s_man)
                            $("#tr_manufacturer select").append('<option value="'+man+'" selected=selected>'+man+'</option>');
                        else
                            $("#tr_manufacturer select").append('<option value="'+man+'">'+man+'</option>');
                    }
                    var cats = $scope.params.wallpapers.catalogs[s_man];
                    for (cat in cats){
                        if (cat == s_cat)
                            $("#tr_collection select").append('<option value="'+cat+'" selected=selected>'+cat+'</option>');
                        else
                            $("#tr_collection select").append('<option value="'+cat+'">'+cat+'</option>');
                    }
                    $("#tr_werehouse select").append("<option value=''></option>");
                    var data = $scope.params[type]['werehouses'];
                    for (var i in data){
                        if (i == s_wh)
                            $("#tr_werehouse select").append('<option value="'+i+'" selected=selected>'+data[i]+'</option>');
                        else
                            $("#tr_werehouse select").append('<option value="'+i+'">'+data[i]+'</option>');
                    }
                    console.log($scope.params.we);
                    //var cats = $scope.params[type][s_man]['catalog'];
                }},
                {
                    width:400, 
                    recreateForm: false, 
                    reloadAfterSubmit: true, 
                    closeAfterAdd:true,
                    beforeInitData: function(){
                        var data =  $("#orders_tab").jqGrid('getRowData',row_id);
                        if (data.user != $scope.user_name){
                            alert("Вы не можете редактировать звонки продавцов");
                            return false;
                        }
                        else 
                            return true;
                    },
                },
                {
                    beforeInitData: function(){
                        var data =  $("#orders_tab").jqGrid('getRowData',row_id);
                        if (data.user != $scope.user_name){
                            alert("Вы не можете редактировать звонки продавцов");
                            return false;
                        }
                        else 
                            return true;
                    }
                }) 
    		}
    	}); 
		$("#orders_tab").jqGrid('navGrid','#orders_tab_pager',{add:false,edit:false,del:false,search:false});
    }

    $scope.returns = function(){
    	$("#returns_tab").jqGrid({ 
    		url:'ajax/returns.php?q=1', 
    		datatype: "json", 
    		height: '100%',
    		mtype: 'POST', 
    		colNames:['№ заказа','Причина возврата','Количество товара','Дата возврата','Дата возврата','Время возврата','Дата ввода','Пользователь'], 
    		colModel:[ 
    			{name:'order_id',index:'order_id', width:100, align:"right", editable:true,editrules: {required:true,custom:true, custom_func:function(value, column){
						if ($.inArray(value, $scope.params.orders) >= 0){
							if ($.inArray(value, $scope.params.returns) >= 0){
								return [false,column+": В системе уже есть возврат по данному заказу"];
							}
							else
								return[true,''];
						}
            			else
            				return [false,column+": Данного номера заказа не существует"];
					}}
				}, 
    			{name:'cause',index:'cause', width:150, align:"right", editable:true, edittype:"textarea",editrules: {required:true}}, 
    			{name:'count',index:'count', width:100, align:"right", sortable:false},
    			{name:'dt_call',index:'dt_call', width:150, align:"right",editable:false}, 
				{name:'dt_call_dt', index:'dt_call_dt', editable:true, editrules: {edithidden:true,required:true},hidden:true, editoptions: {dataInit: initDateEdit}	},
				{name:'dt_call_time', index:'dt_call_time', editable:true, editrules: {edithidden:true,required:true, custom:true, custom_func:function(value, column){
						val = value.split(":");
						if (val[0] < 25 && val[1] < 59) 
							return[true,''];
						else
							return [false,column+": Неверный формат времени"];
					}},hidden:true, editoptions: {dataInit: function(elem){
						$(elem).mask("99:99");
					}}
				},
    			{name:'dt_enter',index:'dt_enter', width:150, align:"right"},
    			{name:'user',index:'user', width:100, align:"right"}
    		], 
    		rowNum:20, 
    		rowList:[20,50,100,150], 
    		pager: '#returns_tab_pager', 
    		sortname: 'order_id', 
    		viewrecords: true, 
    		sortorder: "desc", 
    		multiselect: false, 
    		afterInsertRow: function(rowid,rowdata){
            	if ($.inArray(rowdata.order_id, $scope.params.returns) < 0)
                	$scope.params.returns.push(rowdata.order_id);
            },
    		subGrid: true, 
    		width: 1175,
    		caption: "Возвраты", 
    		editurl : 'ajax/returns.php?q=1',
    		subGridOptions: {
    			"plusicon" : "ui-icon-triangle-1-e", 
    			"minusicon" : "ui-icon-triangle-1-s", 
    			"openicon" : "ui-icon-arrowreturn-1-e" 
    		}, 
    		subGridRowExpanded: function(subgrid_id, row_id) { 
    			var subgrid_table_id, pager_id; 
    			subgrid_table_id = subgrid_id+"_t"; 
    			pager_id = "p_"+subgrid_table_id; 
    			$("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>"); 
    			$("#"+subgrid_table_id).jqGrid({ 
    				url:"ajax/returns.php?q=2&id="+row_id, 
    				datatype: "json", 
    				mtype: 'POST', 
    				colNames: ['ID','TYE_ID','Товар','Производитель','Коллекция','Артикул','Склад','Кол-во','Цена'], 
    				colModel: [ 
    					{name:"id",index:"id",width:100, hidden:true}, 
    					{name:"type_id",index:"type_id",width:100, hidden:true}, 
    					{
    						name:"product",
    						index:"product",
    						width:100,
    						align:"right",
    					}, 
    					{
    						name:"manufacturer",
    						index:"manufacturer",
    						width:150,
    						align:"right",
    					}, 
    					{name:"collection",index:"collection",width:150,align:"right"}, 
    					{name:"articul",index:"articul",width:100,align:"right"}, 
    					{name:"werehouse",index:"werehouse",width:100,align:"right"}, 
    					{name:"count",index:"count",width:100,align:"right",editable:true},
    					{name:"price",index:"price",width:100,align:"right"},
    				], 
    				rowNum:20, 
    				pager: pager_id, 
    				sortname: 'count',
    				sortorder: "asc", 
    				loadComplete: function(data){
    					if (data.records != 0){
    						var col = data.rows.length;
    						$("tr#"+row_id+" td[aria-describedby=returns_tab_count]").text(col);
    					}
    				},
    				height: '100%',
    				viewrecords: true,
    				editurl : 'ajax/returns.php?q=2&id='+row_id,
    			}); 
    			$("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{
                    edit:true,
                    add:false,
                    del:true,
                    search:false
                },
                {
                    width:400, 
                    recreateForm: true, 
                    reloadAfterSubmit: true, 
                    closeAfterEdit:true, 
                    beforeInitData: function(){
                        var data =  $("#returns_tab").jqGrid('getRowData',row_id);
                        if (data.user != $scope.user_name){
                            alert("Вы не можете редактировать звонки продавцов");
                            return false;
                        }
                        else 
                            return true;
                    }
                },{
                    width:400, 
                    recreateForm: false, 
                    reloadAfterSubmit: true, 
                    closeAfterAdd:true,
                    beforeInitData: function(){
                        var data =  $("#returns_tab").jqGrid('getRowData',row_id);
                        if (data.user != $scope.user_name){
                            alert("Вы не можете редактировать звонки продавцов");
                            return false;
                        }
                        else 
                            return true;
                    }
                },{
                    beforeInitData: function(){
                        var data =  $("#returns_tab").jqGrid('getRowData',row_id);
                        if (data.user != $scope.user_name){
                            alert("Вы не можете редактировать звонки продавцов");
                            return false;
                        }
                        else 
                            return true;
                    }
                }) 
    		}
    	}); 
		$("#returns_tab").jqGrid('navGrid','#returns_tab_pager',{add:false,edit:false,del:false,search:false});
    };

    $scope.users = function(){
        $("#users_tab").jqGrid({ 
            url:'ajax/users.php?q=1', 
            datatype: "json", 
            height: '100%',
            mtype: 'POST', 
            colNames:['ID','Логин','Пароль','Роль','Имя','Телефон'], 
            colModel:[ 
                {name:'id',index:'id', width:50, align:"left", hidden:true},
                {name:'login',index:'login', width:150, align:"center",editable:true,editrules: {required:true, custom:true, custom_func:function(value, column){
                    if ($.inArray(value, $scope.params.users) >= 0)
                        return [false,column+": Пользователь с таким логином уже существует"];
                    else
                        return[true,''];
                }}},
                {name:'pwd',index:'pwd', width:150, align:"center",editable:true,editrules: {required:true}}, 
                {name:'role',index:'role', width:100, align:"center",editable:true,editrules: {required:true}, edittype:'select', editoptions:{value:$scope.user_roles}}, 
                {name:'name',index:'name', width:150, align:"right",editable:true}, 
                {name:'phone',index:'phone', width:125, align:"right",editable:true}, 
            ], 
            rowNum:20, 
            rowList:[20,50,100,150], 
            pager: '#users_tab_pager', 
            sortname: 'login', 
            viewrecords: true, 
            sortorder: "asc", 
            multiselect: false, 
            subGrid: true, 
            afterInsertRow: function(rowid,rowdata){
                if (rowdata.role == 'Продавец'){
                    if ($.inArray(rowdata.login, $scope.params.sellers) < 0)
                        $scope.params.sellers[rowdata.login] = rowdata.login;
                }
            },
            width: 1175,
            caption: "Пользователи", 
            editurl : 'ajax/users.php?q=1',
            subGridOptions: {
                "plusicon" : "ui-icon-triangle-1-e", 
                "minusicon" : "ui-icon-triangle-1-s", 
                "openicon" : "ui-icon-arrowreturn-1-e" 
            }, 
            subGridRowExpanded: function(subgrid_id, row_id) { 
                var item = $("#users_tab").jqGrid('getRowData',row_id);
                if (item.role == 'Менеджер'){
                var subgrid_table_id, pager_id; 
                subgrid_table_id = subgrid_id+"_t"; 
                pager_id = "p_"+subgrid_table_id; 
                $("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>"); 
                $("#"+subgrid_table_id).jqGrid({ 
                    url:"ajax/users.php?q=2&id="+row_id, 
                    datatype: "json", 
                    mtype: 'POST', 
                    colNames: ['ID','Логин','Имя','Менеджер'], 
                    colModel: [ 
                        {name:"id",index:"id",width:100, hidden:true}, 
                        {
                            name:'login',
                            index:'login', 
                            width:150, 
                            align:"center",
                            editable:true,
                            editrules: {
                                required:true,
                                custom:true, 
                                custom_func:function(value, column){
                                    var is = new Array();
                                    var data = $("#"+subgrid_table_id).jqGrid('getRowData');
                                    for (i=0;i<data.length;i++){
                                        is.push(data[i]['login']);
                                    }
                                    if (value != '') {
                                        if ($.inArray(value, is) >= 0)
                                            return [false,column+": Этот продавец уже прсвоен этому менеджеру"];
                                        else
                                            return[true,''];
                                    }
                                    else
                                        return [false,column+": Не выбрано пользователя"];
                                }
                            }, 
                            edittype:'select', 
                            editoptions:{value:$scope.params.sellers}
                        },
                        {name:'name',index:'name', width:150, align:"center"}, 
                        {name:'manager',index:'manager', width:150, align:"center",editable:false},       
                    ], 
                    rowNum:20, 
                    pager: pager_id, 
                    sortname: 'login', 
                    sortorder: "asc", 
                    caption: 'Продавцы',
                    height: '100%',
                    /*loadComplete: function(data){
                        if (data.records != 0)
                            $("tr#"+row_id+" td[aria-describedby=calls_tab_count]").text(data.rows.length);
                    },*/
                    viewrecords: true,
                    editurl : 'ajax/users.php?q=2&id='+row_id,
                }); 
                $("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{edit:false,add:true,del:true,search:false},{width:400, recreateForm: true, reloadAfterSubmit: true, closeAfterEdit:true},{width:400, recreateForm: false, reloadAfterSubmit: true, closeAfterAdd:true}) 
                }
                else
                    $("#" + subgrid_id).append("Для данной роли пользователя невозможно добавлять подчиненных продавцов");
            } 
        }); 
        $("#users_tab").jqGrid('navGrid','#users_tab_pager',{add:false,edit:false,del:false,search:false});
    };

    $scope.add_call = function(){
    	$("#calls_tab").jqGrid(
    		'editGridRow','new',{closeAfterAdd:true,width:'400px', reloadAfterSubmit: true, recreateForm:true}
    	);
    }
    $scope.edit_call = function(){
    	var gr = $("#calls_tab").jqGrid('getGridParam','selrow');
        var row = $("#calls_tab").jqGrid('getRowData', gr);
        if (gr != null){
            if (row.user != $("#user_name").val())
                alert("Вы не можете редактировать звонки продавцов");
            else
                $("#calls_tab").jqGrid('editGridRow',gr,{closeAfterEdit:true, width: '400px',reloadAfterSubmit: true,recreateForm:true,});
        }
        else
            alert("Пожалуйста выбирите строку таблицы.");
    }
    $scope.add_order = function(){
    	$("#orders_tab").jqGrid(
    		'editGridRow','new',{closeAfterAdd:true,width:400, reloadAfterSubmit: true, recreateForm:true, onInitializeForm: function(formid){ 
            	$("#tr_del_city").hide();
                $("#tr_del_street").hide();
				$("#tr_del_house").hide();
				$("#tr_del_apartment").hide();
				$("#tr_time").hide();
				$("#tr_deliveryman").hide();
				$("#tr_deliveryman_name").hide();
				$("#tr_deliveryman_werehouse").hide();
				$("#tr_redelivery").hide();
				$("#tr_redelivery_werehouse_from").hide();
				$("#tr_redelivery_dt").hide();
            }}
    	);
    }
    $scope.edit_order = function(){
    	var gr = $("#orders_tab").jqGrid('getGridParam','selrow');
        var row = $("#orders_tab").jqGrid('getRowData', gr);
        if (gr != null){
            if (row.user != $("#user_name").val())
                alert("Вы не можете редактировать звонки продавцов");
            else{
                $("#orders_tab").jqGrid('editGridRow',gr,{closeAfterEdit:true, width: 400,reloadAfterSubmit: true,recreateForm:true,onInitializeForm: function(formid){
            	   if ($("#delivery").is(':checked') == true){
					   $("#tr_del_city").show();
					   $("#tr_del_street").show();
					   $("#tr_del_house").show();
					   $("#tr_del_apartment").show();
					   $("#tr_time").show();
					   $("#tr_deliveryman").show();
					   $("#tr_deliveryman_name").show();
					   $("#tr_deliveryman_werehouse").show();
					   $("#tr_redelivery").show();
					   $("#tr_redelivery_werehouse_from").show();
					   $("#tr_redelivery_dt").show();
				    }
				    else{
					   $("#tr_del_city").hide();
					   $("#tr_del_street").hide();
					   $("#tr_del_house").hide();
					   $("#tr_del_apartment").hide();
					   $("#tr_time").hide();
					   $("#tr_deliveryman").hide();
					   $("#tr_deliveryman_name").hide();
					   $("#tr_deliveryman_werehouse").hide();
					   $("#tr_redelivery").hide();
					   $("#tr_redelivery_werehouse_from").hide();
					   $("#tr_redelivery_dt").hide();
				    }
                }});
            }
        }
        else
            alert("Пожалуйста выбирите строку таблицы.");
    }
    $scope.del_order = function(){
        var gr = $("#orders_tab").jqGrid('getGridParam','selrow');
        if (gr != null){
            $("#orders_tab").jqGrid(
                'delGridRow',gr,{closeAfterDel:true, reloadAfterSubmit: true, recreateForm:true, afterComplete: function(response,postdata){
                    var order_id = response.responseText;
                    for (i=0;i<$scope.params.orders.length;i++){
                        if ($scope.params.orders[i] == order_id)
                            delete $scope.params.orders[i];
                    }
                }}
            );
        }
        else
            alert("Пожалуйста выбирите строку таблицы.");
    }
    $scope.order_detail = function(){
    	var gr = $("#orders_tab").jqGrid('getGridParam','selrow');
        if (gr != null){       	
        	var data = $("#orders_tab").jqGrid('getRowData',gr);
        	$("#order_number").text(data.id);
        	$("#order_client_code").text(data.client_code);
        	$("#order_name").text(data.name);
        	$("#order_phones").text(data.phones);
        	$("#order_comments").html(data.comments.replace(/\n/g,'<br>'));
        	$("#order_count").text(data.count);
        	$("#order_dtcall").text(data.dt_call);
        	$("#order_dtenter").text(data.dt_enter);
        	$("#order_pricedelivery").text(data.price_delivery);
        	$("#order_prepayment").text(data.prepayment);
        	$("#order_sum").text(data.sum);
        	$("#order_sumall").text(data.sum_all);
        	$("#order_delivery").text(data.delivery);
        	$("#order_delcity").text(data.del_city);
        	$("#order_delstreet").text(data.del_street);
        	$("#order_delhouse").text(data.del_house);
        	$("#order_delapartment").text(data.del_apartment);
        	$("#order_time").text(data.time);
        	$("#order_deliveryman").text(data.deliveryman);
        	$("#order_deliveryman_name").text(data.deliveryman_name);
        	$("#order_deliveryman_werehouse").text(data.deliveryman_werehouse);
        	$("#order_redelivery").text(data.redelivery);
        	$("#order_redelivery_werehousefrom").text(data.redelivery_werehouse_from);
        	$("#order_redeliverydt").text(data.redelivery_dt);
        	$("#order_status").text(data.status);
        	$('#order_detail_form').modal();
        }
        else{
        	alert("Пожалуйста выбирите строку таблицы.");
        }
    }
    $scope.add_return = function(){
    	$("#returns_tab").jqGrid(
    		'editGridRow','new',{closeAfterAdd:true,width:'400px', reloadAfterSubmit: true, recreateForm:true}
    	);
    }
    $scope.del_return = function(){
    	var gr = $("#returns_tab").jqGrid('getGridParam','selrow');
        if (gr != null){
    		$("#returns_tab").jqGrid(
    			'delGridRow',gr,{closeAfterDel:true, reloadAfterSubmit: true, recreateForm:true, afterComplete: function(response,postdata){
    				var order_id = response.responseText;
    				for (i in $scope.params.returns){
    					if (order_id == $scope.params.returns[i])
    						delete $scope.params.returns[i];
    				}
    			}}
    		);
    	}
    	else
    		alert("Пожалуйста выбирите строку таблицы.");
    }
    $scope.add_user = function(){
        $("#users_tab").jqGrid(
            'editGridRow','new',{closeAfterAdd:true,width:'400px', reloadAfterSubmit: true, recreateForm:true}
        );
    }
    $scope.edit_user = function(){
        var gr = $("#users_tab").jqGrid('getGridParam','selrow');
        if (gr != null)
            $("#users_tab").jqGrid('editGridRow',gr,{closeAfterEdit:true, width: '400px',reloadAfterSubmit: true,recreateForm:true, afterComplete: function(response,postdata){
                    var login = response.responseText;
                    for (i in $scope.params.sellers){
                        if (login == $scope.params.sellers[i])
                            delete $scope.params.sellers[i];
                    }
                }});
        else
            alert("Пожалуйста выбирите строку таблицы.");
    }
    $scope.del_user = function(){
        var gr = $("#users_tab").jqGrid('getGridParam','selrow');
        if (gr != null){
            $("#users_tab").jqGrid(
                'delGridRow',gr,{closeAfterDel:true, reloadAfterSubmit: true, recreateForm:true, afterComplete: function(response,postdata){
                    var login = response.responseText;
                    for (i in $scope.params.sellers){
                        if (login == $scope.params.sellers[i])
                            delete $scope.params.sellers[i];
                    }
                }}
            );
        }
        else
            alert("Пожалуйста выбирите строку таблицы.");
    }
}]);

$(function(){
	$("#close").bind('click', function(){
		document.location.href = 'auth.php?exit=true';
	});
})

initDateEdit = function(elem) { 
    setTimeout(function() {
    	$(elem).datepicker({
        	formatter: 'date', formatoptions: {
            	srcformat: 'm-d-Y H:i:s',
                newformat: 'yy-M-d'
            },
            autoSize: true,
            maxDate: 0,
            changeYear: false,
            changeMonth: false,
            showButtonPanel: false,
            showWeek: false
        });
    },100)
};

initDateEditFuture = function(elem) { 
    setTimeout(function() {
    	$(elem).datepicker({
        	formatter: 'date', formatoptions: {
            	srcformat: 'm-d-Y H:i:s',
                newformat: 'yy-M-d'
            },
            autoSize: true,
            changeYear: false,
            changeMonth: false,
            showButtonPanel: false,
            showWeek: false
        });
    },100)
};

number_format = function( number, decimals, dec_point, thousands_sep ) {	
		var i, j, kw, kd, km;

		if( isNaN(decimals = Math.abs(decimals)) ){
			decimals = 2;
		}
		if( dec_point == undefined ){
			dec_point = ",";
		}
		if( thousands_sep == undefined ){
			thousands_sep = ".";
		}

		i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

		if( (j = i.length) > 3 ){
			j = j % 3;
		} else{
			j = 0;
		}

		km = (j ? i.substr(0, j) + thousands_sep : "");
		kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);

		kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");

		return km + kw + kd;
	}