var dict = [];
dict['pt-BR'] = [];

dict['pt-BR']['Disable auto refresh'] = 'Desativar auto-atualização';
dict['pt-BR']['Enable auto refresh'] = 'Ativar auto-atualização';

dict['pt-BR'][''] = '';

function tt(obj){
	if(language == 'pt-BR' && dict['pt-BR'][obj]) return dict['pt-BR'][obj];
	else return obj;
}