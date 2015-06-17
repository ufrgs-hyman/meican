var dict = [];
dict['pt-BR'] = [];

dict['pt-BR']['Disable auto refresh'] = 'Desativar auto-atualização';
dict['pt-BR']['Enable auto refresh'] = 'Ativar auto-atualização';
dict['pt-BR']['Close'] = 'Fechar';
dict['pt-BR']['Network'] = 'Rede';
dict['pt-BR']['Device'] = 'Dispositivo';
dict['pt-BR']['Domain'] = 'Domínio';

dict['pt-BR'][''] = '';

function tt(obj){
	if(language == 'pt-BR') return dict['pt-BR'][obj];
	else return obj;
}