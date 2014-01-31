Ext.define('Mip.store.JoinTypes', {
    extend: 'Ext.data.Store',
    fields: [
        {name: 'name', type: 'string'},
        {name: 'description', type: 'string'}
    ],
    data: [
        {name: 'INNER JOIN', description: 'INNER JOIN: Vain yhteensopivat rivit tästä ja edellisistä tauluista'},
        {name: 'LEFT JOIN', description: 'LEFT JOIN: Kaikki rivit aikaisemmista tauluista'},
        {name: 'RIGHT JOIN', description: 'RIGHT JOIN: Kaikki rivit tästä taulusta'},
        {name: 'FULL OUTER JOIN', description: 'FULL OUTER JOIN: Kaikki rivit tästä ja aikaisemmista tauluista'}
    ]
});