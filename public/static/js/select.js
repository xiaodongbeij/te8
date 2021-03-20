    //DropdownSearch 构造
	function DropdownSearch(dropdown){
		this.dropdown = dropdown;
		var _this = this;
		$(document).on('click',''+_this.dropdown+' .dropdown-menu a',function(e){
            _this.select(e);
		})
		$(document).on('keyup',''+_this.dropdown+' .dropdown-menu .seach_name',function(e){
            _this.search(e);
		})
	}
    //选择方法
	DropdownSearch.prototype.select = function(e){
        var _text = $(e.target).text(),   
            parent = $(e.target).closest(this.dropdown); 
        $(parent).find("button em").html(_text);
	 }

    //搜索方法
	DropdownSearch.prototype.search = function(e){
	 	var value = $(e.target).val().trim();
	 	if(value.length < 1){
	 		$(''+this.dropdown+' li:not(":first")').show(); 
	 	}else{
	 		$(''+this.dropdown+' li:not(":first")').hide(); 
	 		$(this.dropdown).find("li:not(':first')[data*='"+value+"']").show(); 
	 	}
	 }
