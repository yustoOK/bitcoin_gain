<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self'; connect-src 'self'; img-src 'self';">
    <title>Bitcoin TZS Gains Calculator</title>
    <link rel="icon" type="image/jpeg" href="bitcoin.jpeg">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="calculator-container">
        <button class="theme-toggle" onclick="toggleTheme()">
            <span class="sun-icon">☀️</span>
            <span class="moon-icon">🌙</span>
        </button>
        <h2>Bitcoin TZS Gains Calculator</h2>
        <div id="results">
            <div id="resultsContent">
                <p><strong>BTC Bought:</strong> <span id="btcBought">0</span> BTC</p>
                <p><strong>TZS Value in 2030 (Midpoint, $700,000 USD):</strong> <span id="tzsValue2030Mid">0</span> TZS</p>
                <p><strong>Nominal TZS Gain (Midpoint):</strong> <span id="tzsGainMid">0</span> TZS</p>
                <p><strong>TZS Value in 2030 (Optimistic, $1,000,000 USD):</strong> <span id="tzsValue2030Opt">0</span> TZS</p>
                <p><strong>Nominal TZS Gain (Optimistic):</strong> <span id="tzsGainOpt">0</span> TZS</p>
                <p><strong>TZS Value in 2030 (Conservative, $500,000 USD):</strong> <span id="tzsValue2030Con">0</span> TZS</p>
                <p><strong>Nominal TZS Gain (Conservative):</strong> <span id="tzsGainCon">0</span> TZS</p>
                <p><strong>TZS Value in 2030 (Average):</strong> <span id="tzsValue2030Avg">0</span> TZS</p>
                <p><strong>Nominal TZS Gain (Average):</strong> <span id="tzsGainAvg">0</span> TZS</p>
            </div>
        </div>
        <form id="calcForm" onsubmit="event.preventDefault();">
            <div class="form-group">
                <label for="tzsAmount">TZS Amount to Invest</label>
                <input type="text" id="tzsAmount" name="tzsAmount" placeholder="e.g., 1000000" required>
                <div id="tzsAmountError" class="error"></div>
            </div>
            <div class="form-group">
                <label for="exchangeRateToday">USD-TZS Exchange Rate Today (TZS per USD)</label>
                <input type="text" id="exchangeRateToday" name="exchangeRateToday" placeholder="e.g., 2679.37" required>
                <div id="exchangeRateTodayError" class="error"></div>
            </div>
            <div class="form-group">
                <label for="btcPriceToday">Bitcoin Price Today (USD per BTC)</label>
                <input type="text" id="btcPriceToday" name="btcPriceToday" placeholder="e.g., 86586.46" required>
                <div id="btcPriceTodayError" class="error"></div>
            </div>
            <div class="form-group">
                <label for="exchangeRate2030">USD-TZS Exchange Rate in 2030 (TZS per USD)</label>
                <input type="text" id="exchangeRate2030" name="exchangeRate2030" placeholder="3150 (predicted)" required>
                <div id="exchangeRate2030Error" class="error"></div>
            </div>
            <div class="button-group">
                <button type="button" class="clear-btn" onclick="clearResults()">Clear</button>
                <button type="button" class="calculate-btn" onclick="calculateGains()">Calculate Gains</button>
                <button type="button" class="history-btn" onclick="toggleHistory()">Show History</button>
            </div>
        </form>
        <div id="history-section">
            <table id="history-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>BTC Bought</th>
                        <th>TZS Value 2030 (Mid)</th>
                        <th>TZS Gain (Mid)</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody id="history-body"></tbody>
            </table>
        </div>
    </div>

    <script>
        const BTC_PRICE_2030_MID = 700000;
        const BTC_PRICE_2030_OPT = 1000000;
        const BTC_PRICE_2030_CON = 500000;

        function calculateGains() {
            console.log('Starting calculateGains function...');
            const tzsAmountInput = document.getElementById('tzsAmount').value.trim();
            const exchangeRateTodayInput = document.getElementById('exchangeRateToday').value.trim();
            const btcPriceTodayInput = document.getElementById('btcPriceToday').value.trim();
            const exchangeRate2030Input = document.getElementById('exchangeRate2030').value.trim();

            console.log('Inputs:', { tzsAmountInput, exchangeRateTodayInput, btcPriceTodayInput, exchangeRate2030Input });

            document.getElementById('tzsAmountError').style.display = 'none';
            document.getElementById('exchangeRateTodayError').style.display = 'none';
            document.getElementById('btcPriceTodayError').style.display = 'none';
            document.getElementById('exchangeRate2030Error').style.display = 'none';

            let hasError = false;
            if (!isValidNumber(tzsAmountInput)) {
                showError('tzsAmountError', 'Please enter a valid positive number for TZS amount.');
                hasError = true;
            }
            const tzsAmount = parseFloat(tzsAmountInput);

            if (!isValidNumber(exchangeRateTodayInput)) {
                showError('exchangeRateTodayError', 'Please enter a valid positive number for today\'s exchange rate.');
                hasError = true;
            }
            const exchangeRateToday = parseFloat(exchangeRateTodayInput);

            if (!isValidNumber(btcPriceTodayInput)) {
                showError('btcPriceTodayError', 'Please enter a valid positive number for Bitcoin price today.');
                hasError = true;
            }
            const btcPriceToday = parseFloat(btcPriceTodayInput);

            if (!isValidNumber(exchangeRate2030Input)) {
                showError('exchangeRate2030Error', 'Please enter a valid positive number for 2030 exchange rate.');
                hasError = true;
            }
            const exchangeRate2030 = parseFloat(exchangeRate2030Input);

            if (hasError) {
                console.log('Validation failed, exiting calculateGains.');
                return;
            }

            console.log('Inputs validated:', { tzsAmount, exchangeRateToday, btcPriceToday, exchangeRate2030 });

            const usdAmount = tzsAmount / exchangeRateToday;
            console.log('USD Amount:', usdAmount);

            const btcBought = usdAmount / btcPriceToday;
            console.log('BTC Bought:', btcBought);

            const tzsValue2030Mid = btcBought * BTC_PRICE_2030_MID * exchangeRate2030;
            const tzsValue2030Opt = btcBought * BTC_PRICE_2030_OPT * exchangeRate2030;
            const tzsValue2030Con = btcBought * BTC_PRICE_2030_CON * exchangeRate2030;
            const tzsValue2030Avg = (tzsValue2030Mid + tzsValue2030Opt + tzsValue2030Con) / 3;

            const tzsGainMid = tzsValue2030Mid - tzsAmount;
            const tzsGainOpt = tzsValue2030Opt - tzsAmount;
            const tzsGainCon = tzsValue2030Con - tzsAmount;
            const tzsGainAvg = tzsValue2030Avg - tzsAmount;

            console.log('Calculated Values:', { tzsValue2030Mid, tzsGainMid, tzsValue2030Opt, tzsGainOpt, tzsValue2030Con, tzsGainCon, tzsValue2030Avg, tzsGainAvg });

            document.getElementById('btcBought').textContent = btcBought.toFixed(8);
            document.getElementById('tzsValue2030Mid').textContent = tzsValue2030Mid.toLocaleString('en-US', { maximumFractionDigits: 0 });
            document.getElementById('tzsGainMid').textContent = tzsGainMid.toLocaleString('en-US', { maximumFractionDigits: 0 });
            document.getElementById('tzsValue2030Opt').textContent = tzsValue2030Opt.toLocaleString('en-US', { maximumFractionDigits: 0 });
            document.getElementById('tzsGainOpt').textContent = tzsGainOpt.toLocaleString('en-US', { maximumFractionDigits: 0 });
            document.getElementById('tzsValue2030Con').textContent = tzsValue2030Con.toLocaleString('en-US', { maximumFractionDigits: 0 });
            document.getElementById('tzsGainCon').textContent = tzsGainCon.toLocaleString('en-US', { maximumFractionDigits: 0 });
            document.getElementById('tzsValue2030Avg').textContent = tzsValue2030Avg.toLocaleString('en-US', { maximumFractionDigits: 0 });
            document.getElementById('tzsGainAvg').textContent = tzsGainAvg.toLocaleString('en-US', { maximumFractionDigits: 0 });

            console.log('UI updated with results.');

            const formData = new FormData();
            formData.append('save_calculation', 'true');
            formData.append('btc_bought', btcBought);
            formData.append('tzs_value_2030_mid', tzsValue2030Mid);
            formData.append('tzs_gain_mid', tzsGainMid);
            formData.append('tzs_value_2030_opt', tzsValue2030Opt);
            formData.append('tzs_gain_opt', tzsGainOpt);
            formData.append('tzs_value_2030_con', tzsValue2030Con);
            formData.append('tzs_gain_con', tzsGainCon);
            formData.append('tzs_value_2030_avg', tzsValue2030Avg);
            formData.append('tzs_gain_avg', tzsGainAvg);

            console.log('Sending AJAX request to save_calculation.php...');

            fetch('save_calculation.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('AJAX response received:', response);
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                console.log('AJAX response data:', data);
                if (data.status === 'success') {
                    console.log('Calculation saved successfully');
                } else {
                    console.error('Error saving calculation:', data.message);
                    alert('Error saving calculation: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Error saving calculation: ' + error.message);
            });

            console.log('calculateGains function completed.');
        }

        function isValidNumber(value) {
            const num = parseFloat(value);
            return !isNaN(num) && num > 0 && value !== '' && !/[^0-9.]/.test(value);
        }

        function showError(elementId, message) {
            const errorElement = document.getElementById(elementId);
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }

        function clearResults() {
            console.log('Clearing results...');
            document.getElementById('calcForm').reset();
            document.getElementById('btcBought').textContent = '0';
            document.getElementById('tzsValue2030Mid').textContent = '0';
            document.getElementById('tzsGainMid').textContent = '0';
            document.getElementById('tzsValue2030Opt').textContent = '0';
            document.getElementById('tzsGainOpt').textContent = '0';
            document.getElementById('tzsValue2030Con').textContent = '0';
            document.getElementById('tzsGainCon').textContent = '0';
            document.getElementById('tzsValue2030Avg').textContent = '0';
            document.getElementById('tzsGainAvg').textContent = '0';
            document.getElementById('tzsAmountError').style.display = 'none';
            document.getElementById('exchangeRateTodayError').style.display = 'none';
            document.getElementById('btcPriceTodayError').style.display = 'none';
            document.getElementById('exchangeRate2030Error').style.display = 'none';
            console.log('Results cleared.');
        }

        function toggleHistory() {
            const historySection = document.getElementById('history-section');
            const historyButton = document.querySelector('.history-btn');
            if (historySection.style.display === 'block') {
                historySection.style.display = 'none';
                historyButton.textContent = 'Show History';
            } else {
                historySection.style.display = 'block';
                historyButton.textContent = 'Hide History';
                fetchHistory();
            }
        }

        function fetchHistory() {
            fetch('get_history.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('History data:', data);
                    const historyBody = document.getElementById('history-body');
                    historyBody.innerHTML = '';
                    if (data.status === 'success' && data.data.length > 0) {
                        data.data.forEach(row => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${row.id}</td>
                                <td>${parseFloat(row.btc_bought).toFixed(8)}</td>
                                <td>${parseFloat(row.tzs_value_2030_mid).toLocaleString('en-US', { maximumFractionDigits: 0 })}</td>
                                <td>${parseFloat(row.tzs_gain_mid).toLocaleString('en-US', { maximumFractionDigits: 0 })}</td>
                                <td>${row.created_at}</td>
                            `;
                            historyBody.appendChild(tr);
                        });
                    } else {
                        const tr = document.createElement('tr');
                        tr.innerHTML = '<td colspan="5">No history available.</td>';
                        historyBody.appendChild(tr);
                    }
                })
                .catch(error => {
                    console.error('Error fetching history:', error);
                    const historyBody = document.getElementById('history-body');
                    historyBody.innerHTML = '<tr><td colspan="5">Error loading history: ' + error.message + '</td></tr>';
                });
        }

        function toggleTheme() {
            document.body.classList.toggle('light-mode');
            if (document.body.classList.contains('light-mode')) {
                localStorage.setItem('theme', 'light');
            } else {
                localStorage.setItem('theme', 'dark');
            }
        }

        window.onload = function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'light') {
                document.body.classList.add('light-mode');
            }
        };
    </script>
</body>
</html>