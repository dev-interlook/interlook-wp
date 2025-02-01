document.addEventListener('DOMContentLoaded', function() {
    const targetDiv = document.querySelector('.sfm-floating-menu.bottom-right.sfm-round.vertical .sfm-button:last-child');

    if (targetDiv) {
        const originalLink = targetDiv.querySelector('a').href;
        const originalIcon = targetDiv.querySelector('i').className;
        const originalText = targetDiv.querySelector('.sfm-tool-tip a').textContent;
        const originalButton = targetDiv.querySelector('.sfm-shape-button');
        const computedStyle = window.getComputedStyle(originalButton);
        const backgroundColor = computedStyle.backgroundColor;

        targetDiv.style.cssText = `
            border-radius: 0 !important;
            overflow: visible !important;
            position: absolute !important;
            right: -60px !important;
            bottom: 0 !important;
            z-index: 999 !important;
            width: 400px !important;
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box !important;
        `;

        targetDiv.innerHTML = `
            <a class="sfm-shape-button" target="_blank" href="${originalLink}" style="
                display: flex !important;
                align-items: center !important;
                padding: 8px 0px !important;
                text-decoration: none !important;
                background: ${backgroundColor} !important;
                color: white !important;
                border-radius: 0 !important;
                width: auto !important;
                min-width: 400px !important;
                height: 45px !important;
                font-size: 14px !important;
                white-space: nowrap !important;
                overflow: visible !important;
                box-sizing: content-box !important;
            ">
                <i class="${originalIcon}" style="
                    margin-right: 12px !important;
                    font-size: 24px !important;
                    height: 100% !important;
                    display: flex !important;
                    align-items: center !important;
                "></i>
                <span style="font-size: 14px !important;">${originalText}</span>
            </a>
        `;

        // Move the second last button up by the height of the rectangle button plus some spacing
        const buttons = document.querySelectorAll('.sfm-floating-menu.bottom-right.sfm-round.vertical .sfm-button');
        const secondLastButton = buttons[buttons.length - 2];
        console.log(secondLastButton);
        if (secondLastButton) {
            secondLastButton.setAttribute('style', `bottom: ${60+7.5}px !important`);
        }
        
        // other buttons
        const buttonCount = buttons.length;
        for (let i = 0; i < buttonCount - 2; i++) {
            const button = buttons[i];
            const bottomPosition = 60 + (1 * (buttonCount - 2 - i)); // Reduced multiplier from 60 to 1
            button.setAttribute('style', `bottom: ${bottomPosition}px !important`);
        }
    }
});
