import chai from 'chai';
const assert = chai.assert;

import {ColumnsMapViewModel} from '../js/ColumnsMap.js';

const columnsMapViewModel = new ColumnsMapViewModel();

describe('columnsMapViewModel', function() {
	describe('locationLinesToPoly()', function() {
		
		//
		// points check
		it('Gets points from two sections', function() {
			var locationLines = `12,34\n\n56,78`;
			var result = columnsMapViewModel.locationLinesToPoly(locationLines);
			assert.equal(result.points.length, 2);
		});
		it('Gets points from multiple sections', function() {
			var locationLines = `11,11\n11,12\n\n21,11\n21,12\n\n\n31,11`;
			var expected = locationLines
				.replace(/\n\n+/g, '\n')
				.split('\n')
			;
			var result = columnsMapViewModel.locationLinesToPoly(locationLines);
			assert.equal(result.points.length, expected.length);
			var resultPoints = result.points.map(p=>p.join(','));
			assert.deepEqual(resultPoints, expected);
		});

		//
		// missing
		it('Missing empty for single section', function() {
			var locationLines = `11,11\n11,12\n21,11\n21,12\n31,11`;
			var result = columnsMapViewModel.locationLinesToPoly(locationLines);
			assert.equal(result.missing.length, 0);
		});
		it('Builds missing for sections', function() {
			var locationLines = `11,11\n11,12\n21,11\n\n21,12\n31,11`;
			var result = columnsMapViewModel.locationLinesToPoly(locationLines);
			assert.equal(result.missing.length, 1);
			var locationLines = `11,11\n11,12\n\n21,11\n\n21,12\n31,11`;
			var result = columnsMapViewModel.locationLinesToPoly(locationLines);
			assert.equal(result.missing.length, 2);
		});
		it('Missing must have two points', function() {
			var locationLines = `11,11\n11,12\n\n21,11\n\n21,12\n31,11`;
			var result = columnsMapViewModel.locationLinesToPoly(locationLines);
			for (var index = 0; index < result.missing.length; index++) {
				const missing = result.missing[index];
				console.log(index, missing);
				assert.equal(missing.length, 2, `Expect two points @index: ${index}`);
			}
		});
		it('Builds missing from start to end', function() {
			var locationLines = `11,11\n11,12\n\n21,11\n21,12\n\n\n31,11`;
			var expected = [
				[['11','12'], ['21','11']],
				[['21','12'], ['31','11']],
			];
			var result = columnsMapViewModel.locationLinesToPoly(locationLines);
			assert.deepEqual(result.missing, expected);
		});

		//
		// paths
		it('Paths skip single points', function() {
			var locationLines = `11,11\n11,12\n\n21,11\n21,12\n\n\n31,11`;
			var result = columnsMapViewModel.locationLinesToPoly(locationLines);
			assert.equal(result.paths.length, 2);
			var locationLines = `11,11\n11,12\n\n21,11\n\n\n31,11\n31,12`;
			var result = columnsMapViewModel.locationLinesToPoly(locationLines);
			assert.equal(result.paths.length, 2);
			var locationLines = `11,11\n11,12\n\n21,11\n21,12\n\n\n31,11\n31,12`;
			var result = columnsMapViewModel.locationLinesToPoly(locationLines);
			assert.equal(result.paths.length, 3);
		});
		it('Paths built for each section', function() {
			var locationLines = `11,11\n11,12\n11,13\n\n21,11\n21,12\n\n\n31,11\n31,12`;
			var expected = [`11,11\n11,12\n11,13`,`21,11\n21,12`,`31,11\n31,12`]
				.map(l=>l.split('\n'))
			;
			var result = columnsMapViewModel.locationLinesToPoly(locationLines);
			var resultPaths = result.paths
				.map(s=>s.map(p=>p.join(',')))
			;
			assert.deepEqual(resultPaths, expected);
		});
	});
});