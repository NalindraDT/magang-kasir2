<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Transaksi</title>
    <style>
        body {
            font-family: monospace;
            font-size: 12px;
            width: 300px;
            margin: 0 auto;
        }

        .container {
            padding: 10px;
        }

        .header,
        .footer {
            text-align: center;
        }

        .line {
            border-top: 1px dashed black;
            margin: 10px 0;
        }

        .item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .total {
            font-weight: bold;
        }

        @media print {
            body {
                width: 100%;
                margin: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print(); window.onafterprint = function(){ window.close(); };">
    <div class="container">
        <div class="header">
            <h3>Toko Magang</h3>
            <p>Jl. Utara 50, Cilacap<br>81529620220414142434</p>
        </div>
        <div class="line"></div>
        <div class="info">
            <?php $tanggal_wib = (new DateTime($pesanan['tanggal'], new DateTimeZone('UTC')))->setTimezone(new DateTimeZone('Asia/Jakarta')); ?>
            <p><?= $tanggal_wib->format('Y-m-d') ?>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Nalindra</p>
            <p><?= $tanggal_wib->format('H:i:s') ?>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</p>
            <p>No.0-<?= $pesanan['id_pesanan'] ?></p>
        </div>
        <div class="line"></div>
        <div class="items">
            <?php foreach ($detail_pesanan as $item): ?>
                <div class="item">
                    <span><?= $item['nama_produk'] ?></span>
                </div>
                <div class="item">
                    <span><?= $item['kuantitas'] ?> X <?= number_format($item['harga_satuan'], 0, ',', '.') ?></span>
                    <span>Rp <?= number_format($item['total_harga'], 0, ',', '.') ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="line"></div>
        <div class="item sub-total">
            <span>Sub Total</span>
            <span><?= number_format($pesanan['total_bayar'], 0, ',', '.') ?></span>
        </div>
        <div class="item total">
            <span>Total</span>
            <span><?= number_format($pesanan['total_bayar'], 0, ',', '.') ?></span>
        </div>
        <div class="item">
            <span>Bayar (Cash)</span>
            <span><?= number_format($pesanan['total_bayar'], 0, ',', '.') ?></span>
        </div>
        <div class="item">
            <span>Kembali</span>
            <span>0</span>
        </div>
        <div class="line"></div>
        <div class="footer">
            <p>Link Kritik dan Saran:<br>https://github.com/NalindraDT</p>
        </div>
    </div>
</body>

</html>