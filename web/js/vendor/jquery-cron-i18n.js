var dict = [];
dict['pt-BR'] = [];

dict['pt-BR']['Every'] = 'Repetir a cada';
dict['pt-BR']['until'] = 'até';
dict['pt-BR']['of'] = 'de';
dict['pt-BR']['on day'] = 'no dia';
dict['pt-BR']['at'] = 'às';
dict['pt-BR']['on'] = 'no seguinte dia:';
dict['pt-BR']['time'] = 'vez';
dict['pt-BR']['times'] = 'vezes';
dict['pt-BR']['hour'] = 'hora';
dict['pt-BR']['hours'] = 'horas';
dict['pt-BR']['on minute'] = 'no minuto';
dict['pt-BR']['minute'] = 'minuto';
dict['pt-BR']['minutes'] = 'minutos';
dict['pt-BR']['day'] = 'dia';
dict['pt-BR']['days'] = 'dias';
dict['pt-BR']['week'] = 'semana';
dict['pt-BR']['weeks'] = 'semanas';
dict['pt-BR']['month'] = 'mês';
dict['pt-BR']['months'] = 'meses';
dict['pt-BR']['year'] = 'ano';
dict['pt-BR']['Sunday'] = 'Domingo';
dict['pt-BR']['Monday'] = 'Segunda';
dict['pt-BR']['Tuesday'] = 'Terça';
dict['pt-BR']['Wednesday'] = 'Quarta';
dict['pt-BR']['Thursday'] = 'Quinta';
dict['pt-BR']['Friday'] = 'Sexta';
dict['pt-BR']['Saturday'] = 'Sábado';
dict['pt-BR']['January'] = 'Janeiro';
dict['pt-BR']['February'] = 'Fevereiro';
dict['pt-BR']['March'] = 'Março';
dict['pt-BR']['April'] = 'Abril';
dict['pt-BR']['May'] = 'Maio';
dict['pt-BR']['June'] = 'Junho';
dict['pt-BR']['July'] = 'Julho';
dict['pt-BR']['August'] = 'Agosto';
dict['pt-BR']['September'] = 'Setembro';
dict['pt-BR']['October'] = 'Outubro';
dict['pt-BR']['November'] = 'Novembro';
dict['pt-BR']['December'] = 'Dezembro';

dict['pt-BR']['error'] = 'erro';

dict['pt-BR'][''] = '';

function tt(obj){
    if(language == 'pt-BR' && dict['pt-BR'][obj]) return dict['pt-BR'][obj];
    else return obj;
}