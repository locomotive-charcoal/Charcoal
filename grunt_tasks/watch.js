/**
* grunt watch task
*/
module.exports = {
	php: {
		files :[
			'src/**/*.php',
			'tests/**/*.php',
		],
		tasks: ['phplint']
	}
};
