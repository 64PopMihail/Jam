
import noUiSlider from 'nouislider';
import 'nouislider/dist/nouislider.css';

noUiSlider.create(slider, {
    start: [parseInt(minChoice)/100, parseInt(maxChoice)/100],
    connect: true,
    range: {
        'min': parseInt(minRange)/100,
        'max': parseInt(maxRange)/100,
        
    },
    behaviour: 'tap-drag',
    tooltips: true
});
function refresh(values) {
    minHidden.value=values[0];
    maxHidden.value=values[1];
}

slider.noUiSlider.on("update", refresh);


