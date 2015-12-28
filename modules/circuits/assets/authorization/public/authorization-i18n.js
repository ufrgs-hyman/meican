var dict = [];
dict['pt-BR'] = [];

dict['pt-BR']['Request will be accepted. If you want, provide a message:'] = 'A requisição vai ser aceita. Caso deseje, adicione uma mensagem:';
dict['pt-BR']['Request will be rejected. If you want, provide a message:'] = 'A requisição vai ser rejeitada. Caso deseje, adicione uma mensagem:';
dict['pt-BR']['All requests will be accepted. If you want, provide a message:'] = 'Todas as requisições serão aceitas. Caso deseje, adicione uma mensagem:';
dict['pt-BR']['All requests will be rejected. If you want, provide a message:'] = 'Todas as requisições serão rejeitadas. Caso deseje, adicione uma mensagem:';
dict['pt-BR']['Cancel'] = 'Cancelar';
dict['pt-BR'][''] = '';
dict['pt-BR'][''] = '';
dict['pt-BR'][''] = '';
dict['pt-BR'][''] = '';
dict['pt-BR'][''] = '';
dict['pt-BR'][''] = '';

function tt(obj){
	if(language == 'pt-BR' && dict['pt-BR'][obj]) return dict['pt-BR'][obj];
	else return obj;
}