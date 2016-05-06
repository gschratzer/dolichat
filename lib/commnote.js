/*
 * Copyright (C) 2010-2100 Firma 7reasons        <lukas.proemer@7reasons.at>
 * 
 */
 
commnote = {
	
	ajaxreq: {
		url: 'commnote_ajax_n.php',
		options: {

	
			method: "POST",
			postBody: ''
		}
	},
	addpos: function(id,line,prodref,commbonus){
		this.ajaxreq.options.onComplete = this.onComplete
		this.ajaxreq.options.postBody = "action=addpos&id="+id+"&line="+line+"&prodref="+prodref+"&commbonus="+commbonus ; 
		new Ajax.Request(this.ajaxreq.url, this.ajaxreq.options);	
	},
	delpos: function(id,line,delrec,commbonus){
		this.ajaxreq.options.onComplete = this.onComplete
		this.ajaxreq.options.postBody = "action=delpos&id="+id+"&line="+line+"&delrec="+delrec+"&commbonus="+commbonus ; 
		new Ajax.Request(this.ajaxreq.url, this.ajaxreq.options);	
	},
	addallpos: function(id){
		this.ajaxreq.options.onComplete = this.onComplete
		this.ajaxreq.options.postBody = "action=addpos&id="+id+"&line=all"; 
		new Ajax.Request(this.ajaxreq.url, this.ajaxreq.options);	
	},
	delallpos: function(id){
		this.ajaxreq.options.onComplete = this.onComplete
		this.ajaxreq.options.postBody = "action=delpos&id="+id+"&line=all"; 
		new Ajax.Request(this.ajaxreq.url, this.ajaxreq.options);	
	},
	
	
	onComplete: function(data, json){
		if(data.responseText.substr(0,9) == "<!DOCTYPE" ) location.reload();
		
		replaces = data.responseText.split('||');
		
		for(e=0,max=replaces.length;e<max;e++){
			
			row = replaces[e].split('---');
			
			
			document.getElementById('commlinep_'+row[0]).innerHTML = row[1];
			document.getElementById('commlinea_'+row[0]).innerHTML = row[2];
			document.getElementById('commlineb_'+row[0]).innerHTML = row[3];
		
			
		}
		
		
	}
	
	
	
	

}

commnote_n = {
	
	ajaxreq: {
		url: 'commnote_ajax_n.php',
		options: {

	
			method: "POST",
			postBody: ''
		}
	},

	lockStatus: new Array(),
	
	DelAllStatut: function(tid){
			
			lines = document.getElementsByClassName('ajaxline');
		  for(c=0,m=lines.length;c<m,tform=lines[c];c++){
		
			  this.lockStatus[tform.ajaxline.value]=true;
				tstatut = document.getElementById('commnote_statut_'+tform.ajaxline.value);
			  tstatut.innerHTML = '<img height="12" width="12"  src="/theme/PORTURA/img/working.gif">' ;
			  
			}
			
			this.ajaxreq.options.onComplete = this.DelAllStatutComplete;
			this.ajaxreq.options.postBody = "action=delall&id="+tid; 
			new Ajax.Request(this.ajaxreq.url, this.ajaxreq.options);	
		
	},
	DelAllStatutComplete: function(data, json){
		lines = document.getElementsByClassName('ajaxline');
		
	for(c=0,m=lines.length;c<m,tform=lines[c];c++){
			
			commnote_n.lockStatus[tform.ajaxline.value] = false;
			
			tstatut = document.getElementById('commnote_statut_'+tform.ajaxline.value);
			tprice = document.getElementById('commnote_price_'+tform.ajaxline.value);
				
			tstatut.innerHTML = '<img src="/theme/PORTURA/img/statut5.png">' ;
			tform.rowid.value= '';
			tprice.innerHTML = '0,00';
			
		}
		
	},
	ChangeAllStatut: function(tid){
		lines = document.getElementsByClassName('ajaxline');
	
		body  = "action=addall";
		
	for(c=0,m=lines.length;c<m,tform=lines[c];c++){
	
		al = tform.ajaxline.value;
		
		if(this.lockStatus[tform.ajaxline.value] == true) continue;
	 	this.lockStatus[tform.ajaxline.value]	= true;
		
		tstatut = document.getElementById('commnote_statut_'+al);
		tstatut.innerHTML = '<img height="12" width="12" src="/theme/PORTURA/img/working.gif">' ;
		
		body += "&ajaxline[]="+al;
		if(tform.rowid.value > 0){
			body += "&subaction["+al+"]=del";
			body += "&rowid["+al+"]="+tform.rowid.value;
		}else{
			body += "&subaction["+al+"]=add";
			body += "&commbonus["+al+"]="+tform.commbonus.value;
			body += "&fk_com_rowid["+al+"]="+tform.fk_com_rowid.value;
			body += "&fk_com_sp["+al+"]="+tform.fk_com_sp.value;
			body += "&comcat["+al+"]="+tform.comcat.value;
			body += "&fk_facturedetid["+al+"]="+tform.fk_facturedetid.value;
			body += "&fk_clientid["+al+"]="+tform.fk_clientid.value;
			body += "&fk_comnote["+al+"]="+tform.fk_comnote.value;
			body += "&fk_product["+al+"]="+tform.fk_product.value;
			body += "&fk_product_nom["+al+"]="+tform.fk_product_nom.value;
			body += "&fk_product_ref["+al+"]="+tform.fk_product_ref.value;
			body += "&value["+al+"]="+tform.tvalue.value;
			body += "&fk_basis_price["+al+"]="+tform.fk_basis_price.value;
			body += "&fk_percentage["+al+"]="+tform.fk_percentage.value;
			body += "&fk_com_amount["+al+"]="+tform.fk_com_amount.value;
			body += "&fk_deposit["+al+"]="+tform.fk_deposit.value;
			body += "&fk_user_author["+al+"]="+tform.fk_user_author.value;
			body += "&rang["+al+"]="+tform.rang.value;
		}
		  
		 this.ChangeStatut(tform.ajaxline);
			
	
		}
		  this.ajaxreq.options.onComplete = this.ChangeStatutComplete;
			this.ajaxreq.options.postBody = body; 
		
			new Ajax.Request(this.ajaxreq.url, this.ajaxreq.options);		
			
	},
	ChangeStatut: function(obj){
		 
		
		tform = obj.form;
 		if(this.lockStatus[tform.ajaxline.value] == true) return false;
	 	this.lockStatus[tform.ajaxline.value]	= true;
		
		tstatut = document.getElementById('commnote_statut_'+tform.ajaxline.value);
		tstatut.innerHTML = '<img height="12" width="12" src="/theme/PORTURA/img/working.gif">' ;
	
		if(tform.rowid.value > 0){
			this.ajaxreq.options.onComplete = this.ChangeStatutComplete;
			this.ajaxreq.options.postBody = "action=del&rowid="+tform.rowid.value+"&ajaxline="+tform.ajaxline.value; 
			new Ajax.Request(this.ajaxreq.url, this.ajaxreq.options);
			
		}else{
			body  = "action=add";
			body += "&commbonus="+tform.commbonus.value;
			body += "&fk_com_rowid="+tform.fk_com_rowid.value;
			body += "&fk_com_sp="+tform.fk_com_sp.value;
			body += "&comcat="+tform.comcat.value;
			body += "&fk_facturedetid="+tform.fk_facturedetid.value;
			body += "&fk_clientid="+tform.fk_clientid.value;
			body += "&fk_comnote="+tform.fk_comnote.value;
			body += "&fk_product="+tform.fk_product.value;
			body += "&fk_product_nom="+tform.fk_product_nom.value;
			body += "&fk_product_ref="+tform.fk_product_ref.value;
			body += "&value="+tform.tvalue.value;
			body += "&fk_basis_price="+tform.fk_basis_price.value;
			body += "&fk_percentage="+tform.fk_percentage.value;
			body += "&fk_com_amount="+tform.fk_com_amount.value;
			body += "&fk_deposit="+tform.fk_deposit.value;
			body += "&fk_user_author="+tform.fk_user_author.value;
			body += "&rang="+tform.rang.value;
			body += "&ajaxline="+tform.ajaxline.value;
			
			this.ajaxreq.options.onComplete = this.ChangeStatutComplete;
			this.ajaxreq.options.postBody = body; 
			new Ajax.Request(this.ajaxreq.url, this.ajaxreq.options);
			
		}
		
	},
	
	ChangeStatutComplete: function(data, json){
		if(data.responseText.substr(0,9) == "<!DOCTYPE" ) location.reload();
			
			lines = data.responseText.split('||');
			for(c=0,m=lines.length;c<m,sdata=lines[c];c++){
					
				row = sdata.split('---');
				
				tform = document.getElementById('commnote_form_'+row[0]);
				tstatut = document.getElementById('commnote_statut_'+row[0]);
				tprice = document.getElementById('commnote_price_'+row[0]);
				
				commnote_n.lockStatus[ row[0] ] = false;
				
				if(row[1]){
					tstatut.innerHTML = '<img src="/theme/PORTURA/img/statut4.png">' ;
					tform.rowid.value= row[1];
					tprice.innerHTML = tform.ajax_price.value;
				}else{
					tstatut.innerHTML = '<img src="/theme/PORTURA/img/statut5.png">' ;
					tform.rowid.value= '';
					tprice.innerHTML = '0,00';				
				}
				
		}
	
	}
	
	
	
	

}

commnote_x = {
	
	ajaxreq: {
		url: 'commnote_ajax_n.php',
		options: {

	
			method: "POST",
			postBody: ''
		}
	},
	lockStatus: new Array(),
	DelAllStatut: function(tid){
			
			lines = document.getElementsByClassName('ajaxline');
		  for(c=0,m=lines.length;c<m,tform=lines[c];c++){
		
			  this.lockStatus[tform.ajaxline.value]=true;
				tstatut = document.getElementById('commnote_statut_'+tform.ajaxline.value);
			  tstatut.innerHTML = '<img height="12" width="12"  src="/theme/PORTURA/img/working.gif">' ;
			  
			}
			
			
			body = "action=delall&id="+tid; 
			
		  $.ajax({
					type: "POST",
					url: "/commissions/commnote_ajax_n.php",
					cache: false,
					data: body
				})
				.done(function( msg ) {
					location.reload();
	  		});
	},
	DelAllStatutComplete: function(data, json){
		lines = document.getElementsByClassName('ajaxline');
		
	for(c=0,m=lines.length;c<m,tform=lines[c];c++){
			
			commnote_n.lockStatus[tform.ajaxline.value] = false;
			
			tstatut = document.getElementById('commnote_statut_'+tform.ajaxline.value);
			tprice = document.getElementById('commnote_price_'+tform.ajaxline.value);
				
			tstatut.innerHTML = '<img src="/theme/PORTURA/img/statut5.png">' ;
			tform.rowid.value= '';
			tprice.innerHTML = '0,00';
			
		}
		
	},
	ChangeAllStatut: function(tid){
		lines = document.getElementsByClassName('ajaxline');
	
		body  = "action=addall";
		
	for(c=0,m=lines.length;c<m,tform=lines[c];c++){
	
		al = tform.ajaxline.value;
		
		if(this.lockStatus[tform.ajaxline.value] == true) continue;
	 	this.lockStatus[tform.ajaxline.value]	= true;
		
		tstatut = document.getElementById('commnote_statut_'+al);
		tstatut.innerHTML = '<img height="12" width="12" src="/theme/PORTURA/img/working.gif">' ;
		
		body += "&ajaxline[]="+al;
		if(tform.rowid.value > 0){
			body += "&subaction["+al+"]=del";
			body += "&rowid["+al+"]="+tform.rowid.value;
		}else{
			body += "&subaction["+al+"]=add";
			body += "&commbonus["+al+"]="+tform.commbonus.value;
			body += "&fk_com_rowid["+al+"]="+tform.fk_com_rowid.value;
			body += "&fk_com_sp["+al+"]="+tform.fk_com_sp.value;
			body += "&comcat["+al+"]="+tform.comcat.value;
			body += "&fk_facturedetid["+al+"]="+tform.fk_facturedetid.value;
			body += "&fk_clientid["+al+"]="+tform.fk_clientid.value;
			body += "&fk_comnote["+al+"]="+tform.fk_comnote.value;
			body += "&fk_product["+al+"]="+tform.fk_product.value;
			body += "&fk_product_nom["+al+"]="+tform.fk_product_nom.value;
			body += "&fk_product_ref["+al+"]="+tform.fk_product_ref.value;
			body += "&value["+al+"]="+tform.tvalue.value;
			body += "&fk_basis_price["+al+"]="+tform.fk_basis_price.value;
			body += "&fk_percentage["+al+"]="+tform.fk_percentage.value;
			body += "&fk_com_amount["+al+"]="+tform.fk_com_amount.value;
			body += "&fk_deposit["+al+"]="+tform.fk_deposit.value;
			body += "&fk_user_author["+al+"]="+tform.fk_user_author.value;
			body += "&rang["+al+"]="+tform.rang.value;
		}
		  
		 // this.ChangeStatut(tform.ajaxline);
			
	
		}
		  
	
				$.ajax({
					type: "POST",
					url: "/commissions/commnote_ajax_n.php",
					cache: false,
					data: body
				})
				.done(function( msg ) {
					 location.reload();
				});
	
		  
	},
	ChangeStatut: function(obj){
		 
		var that = this;
		tform = obj.form;
 		if(this.lockStatus[tform.ajaxline.value] == true) return false;
	 	this.lockStatus[tform.ajaxline.value]	= true;
		
		tstatut = document.getElementById('commnote_statut_'+tform.ajaxline.value);
		tstatut.innerHTML = '<img height="12" width="12" src="/theme/PORTURA/img/working.gif">' ;

		if(tform.rowid.value > 0){
			
			body = "action=del&rowid="+tform.rowid.value+"&ajaxline="+tform.ajaxline.value; 
					
			$.ajax({
					type: "POST",
					url: "/commissions/commnote_ajax_n.php",
					cache: false,
					data: body
				})
				.done(function( msg ) {
						 this.ChangeStatutComplete;
	
	    				tstatut = document.getElementById('commnote_statut_'+tform.ajaxline.value);
							tstatut.innerHTML = '<img height="12" width="12" src="/theme/PORTURA/img/statut5.png">' ;
	    				document.getElementById('commnote_price_'+tform.ajaxline.value).innerHTML = "0.00";
	  		});
			
		}else{
			body  = "action=add";
			body += "&commbonus="+tform.commbonus.value;
			body += "&fk_com_rowid="+tform.fk_com_rowid.value;
			body += "&fk_com_sp="+tform.fk_com_sp.value;
			body += "&comcat="+tform.comcat.value;
			body += "&fk_facturedetid="+tform.fk_facturedetid.value;
			body += "&fk_clientid="+tform.fk_clientid.value;
			body += "&fk_comnote="+tform.fk_comnote.value;
			body += "&fk_product="+tform.fk_product.value;
			body += "&fk_product_nom="+tform.fk_product_nom.value;
			body += "&fk_product_ref="+tform.fk_product_ref.value;
			body += "&value="+tform.tvalue.value;
			body += "&fk_basis_price="+tform.fk_basis_price.value;
			body += "&fk_percentage="+tform.fk_percentage.value;
			body += "&fk_com_amount="+tform.fk_com_amount.value;
			body += "&fk_deposit="+tform.fk_deposit.value;
			body += "&fk_user_author="+tform.fk_user_author.value;
			body += "&rang="+tform.rang.value;
			body += "&ajaxline="+tform.ajaxline.value;

			$.ajax({
					type: "POST",
					url: "/commissions/commnote_ajax_n.php",
					cache: false,
					data: body
				})
				.done(function( msg ) {
					this.ChangeStatutComplete;
					tstatut = document.getElementById('commnote_statut_'+tform.ajaxline.value);
					tstatut.innerHTML = '<img height="12" width="12" src="/theme/PORTURA/img/statut4.png">' ;
	    		document.getElementById('commnote_price_'+tform.ajaxline.value).innerHTML = tform.fk_com_amount.value;
	  		});
		}
	},
	ChangeStatutComplete: function(data, json){
		alert(data);
		if(data.responseText.substr(0,9) == "<!DOCTYPE" ) location.reload();
			
			lines = data.responseText.split('||');
			for(c=0,m=lines.length;c<m,sdata=lines[c];c++){
					
				row = sdata.split('---');
				
				tform = document.getElementById('commnote_form_'+row[0]);
				tstatut = document.getElementById('commnote_statut_'+row[0]);
				tprice = document.getElementById('commnote_price_'+row[0]);
				
				commnote_n.lockStatus[ row[0] ] = false;
				
				if(row[1]){
					tstatut.innerHTML = '<img src="/theme/PORTURA/img/statut4.png">' ;
					tform.rowid.value= row[1];
					tprice.innerHTML = tform.ajax_price.value;
				}else{
					tstatut.innerHTML = '<img src="/theme/PORTURA/img/statut5.png">' ;
					tform.rowid.value= '';
					tprice.innerHTML = '0,00';				
				}
				
		}
	
	}
	
	
	
	

}

