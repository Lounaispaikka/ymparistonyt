Ext.onReady(function(){
	
	createMessageForm( 'newcommentform' );
	
});

function likeComment( MsgId ) {
	
	console.log("likeComment", MsgId);
	likeAjax( MsgId, 1 );
	return false;
	
}

function dislikeComment( MsgId ) {
	
	console.log("dislikeComment", MsgId);
	likeAjax( MsgId, -1 );
	return false;
	
}

function likeAjax( MsgId, LikeValue ) {
	
	
	Ext.Ajax.request({
		url: '/run/lougis/comment/likeMsg/',
		params: {
			msgid: MsgId,
			likeval: LikeValue
		},
		success: function(response) {
			var res = Ext.JSON.decode(response.responseText);
			var lbox = Ext.get('lbox'+res.comment.id);
			var spans = lbox.query('span');
			Ext.get(spans[0]).update(res.comment.likes);
			Ext.get(spans[1]).update(res.comment.dislikes);
			var as = lbox.query('a');
			Ext.get(as).set({ onclick: '' });
			lbox.addCls('clicked');
		}
	});
	console.log("likeAjax", MsgId, LikeValue);
	
}

function showReplyBox( ParentMsgId ) {
	
	Ext.Ajax.request({
		url: '/run/lougis/comment/replyBoxHtml/',
		params: {
			msgid: ParentMsgId
		},
		success: function(res) {
			var replybox = Ext.get('replybox'+ParentMsgId);
			replybox.update(res.responseText, true, function() {
				createMessageForm('replyform'+ParentMsgId, ParentMsgId);
			});
		}
	});
	
}

function closeReplyBox( ParentMsgId ) {
	
	var replybox = Ext.get('replybox'+ParentMsgId);
	replybox.update('');
	
}

function showNewMsg(  ) {
	
	var msgBox = Ext.get('newcomment');
	msgBox.animate({
		to: {
			opacity: 1	
		},
		listeners: {
			beforeanimate: function(anim) {
				anim.target.target.setStyle({
					display: 'block'	
				});
			}
		}
	});
	return false;
	
}

function hideNewMsg(  ) {
	
	var msgBox = Ext.get('newcomment');
	msgBox.animate({
		to: {
			opacity: 0	
		},
		listeners: {
			afteranimate: function(anim) {
				anim.target.target.setStyle({
					display: 'none'	
				});
			}	
		}
	});
	return false;
	
}

function createMessageForm( targetId, replyTo ) {
	
	if ( typeof replyTo == 'undefined' ) replyTo = null;
	
	var formPanel = Ext.create('Ext.form.Panel', {
		title: null,
		border: 0,
		width: 400,
		url: '/run/lougis/comment/newcomment/',
		renderTo: targetId,
		buttonAlign: 'right',
		defaults: {
			xtype: 'textfield',
			labelWidth: 80,
			labelAlign: 'right',
			width: 380,
			minLengthText: "Tämän kentän vähimmäispituus on {0} merkkiä!",
			maxLengthText: "Tämän kentän maksimipituus on {0} merkkiä!"
		},
		items: [
			{
				xtype: 'textarea',
				name: 'comment[msg]',
				minLength: 2,
				fieldLabel: 'Viesti',
				grow: true
			},
			{
				name: 'comment[nick]',
				minLength: 2,
				maxLength: 200,
                                width: 360,
				fieldLabel: 'Nimi tai nimimerkki',
                                labelWidth: 110,
                                margin: '0 0 0 35'
			},
			{
				name: 'comment[check]',
				minLength: 2,
                                width: 50,
				fieldLabel: 'Mikä on tämän vuoden vuosiluku?',
                                labelWidth: 220,
                                width: 320,
                                margin: '0 0 0 75'
			}
			/*{
				xtype: 'fieldcontainer',
				layout: 'hbox',
				fieldLabel: 'Vastaa kysymykseen',
				items: [
					{
						xtype: 'displayfield',
						value: 'Mikä on tämän vuoden vuosiluku?'
					},
					{
						name: 'comment[check]',
						xtype: 'textfield',
						width: 50,
						margin: '0 0 0 5'
					}
				]
			}/*,				
			{
				xtype: 'displayfield',
				fieldStyle: 'color:#888',
				margin: '0 0 0 125',
				value: 'roskapostin estomekanismi'
			}*/
		],
		fbar: [
			{
				text: 'Lähetä',
				handler: function( btn ) {
					var form = btn.up('form').getForm();
					if ( form.isValid() ) {
						form.submit({
							success: function(form, action) {
								document.location.reload(true);
								//window.open('?'+Math.round(Math.random()*1000)+'#cm'+action.result.comment.id, '_self');
							},
							failure: function(form, action) {
								Ext.Msg.alert('Virhe', action.result.msg);
							}
						});
					}
				}
			}
		],
		listeners: {
			beforerender: function(panel) {
				if ( replyTo != null ) {
					var extras = [
						{
							xtype: 'hiddenfield',
							name: 'comment[parent_id]',
							value: replyTo
						},
						{
							name: 'comment[title]',
							minLength: 2,
							maxLength: 200,
							fieldLabel: 'Otsikko',
                                                        margin: '0 0 -10 15'
						}
					];
                                    panel.insert(0, extras[1]);
                                    panel.insert(0, extras[0]);
				} else {
					var extras = [
						{
							name: 'comment[title]',
							minLength: 2,
							maxLength: 200,
							fieldLabel: 'Otsikko',
                                                        margin: '0 0 -10 15'
						}
					];
                                    panel.insert(0, extras[0]);
				}
			},
			afterrender: function(panel) {
				/*if ( targetId == 'newcommentform' ) {
					hideNewMsg();
				}*/
				
			}
		}
	});
	
}
