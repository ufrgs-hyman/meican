var dict = [];
dict['pt-BR'] = [];

dict['pt-BR']['Disable auto refresh'] = 'Desativar autoatualização';
dict['pt-BR']['Enable auto refresh'] = 'Ativar autoatualização';

dict['pt-BR'][''] = '';

function tt(obj){
	if(language == 'pt-BR' && dict['pt-BR'][obj]) return dict['pt-BR'][obj];
	else return obj;
}