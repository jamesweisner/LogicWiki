$(document).ready(function()
{
	if($('#argument').length)
		Argument.load();
});

var Argument = {
	conclusion: '',
	premises: [],
	load: function()
	{
		var json = jQuery.parseJSON($('#argument').val());
		$('#interface').empty();
		var premisis   = $('<div/>', { id: 'premisis' });
		var conclusion = $('<div/>', { id: 'conclusion' });
		for(var i in json.premisis)
			premisis.append(Argument.proposition(json.premisis[i]));
		conclusion.html(Argument.proposition(conclusion));
		premisis.appendTo('#interface');
		conclusion.appendTo('#interface');
	},
	save: function()
	{
		
	},
	proposition: function(json)
	{
		var text = '';
		switch(json.type)
		{
			case 1: // If (propositon) then (proposition).
				text += "If <b>" + json.vars[0] + "</b> then <b>" + json.vars[1] + "</b>.";
				break;
			case 2: // All (set) are (set).
				text += "All <b>" + json.vars[0] + "</b> are <b>" + json.vars[1] + "</b>.";
				break;
			case 3: // No (set) are (set).
				text += "No <b>" + json.vars[0] + "</b> are <b>" + json.vars[1] + "</b>.";
				break;
			case 4: // Some (set) are (set).
				text += "Some <b>" + json.vars[0] + "</b> are <b>" + json.vars[1] + "</b>.";
				break;
			case 5: // Some (set) aren't (set).
				text += "Some <b>" + json.vars[0] + "</b> aren't <b>" + json.vars[1] + "</b>.";
				break;
			case 6: // Most (set) are (set).
				text += "Most <b>" + json.vars[0] + "</b> are <b>" + json.vars[1] + "</b>.";
				break;
			case 7: // Most (set) aren't (set).
				text += "Most <b>" + json.vars[0] + "</b> aren't <b>" + json.vars[1] + "</b>.";
				break;
			case 8: // (thing) is a (set).
				text += "<b>" + json.vars[0] + "</b> is a <b>" + json.vars[1] + "</b>.";
				break;
			case 9: // (thing) isn't a (set).
				text += "<b>" + json.vars[0] + "</b> isn't a <b>" + json.vars[1] + "</b>.";
				break;
			case 10: // (thing) may be a (set).
				text += "<b>" + json.vars[0] + "</b> may be a <b>" + json.vars[1] + "</b>.";
				break;
			case 11: // (thing) may not be a (set).
				text += "<b>" + json.vars[0] + "</b> may not be a <b>" + json.vars[1] + "</b>.";
				break;
			default:
				text += 'Unknown proposition type (' + json.vars.join(', ') + ').';
		}
		return '<p>' + text + '</p>';
	}
};
