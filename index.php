<?php
$settings = json_decode(file_get_contents('settings.json'), true);
?>

<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>註冊表格</title>
    <link href="styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script>
        // 將設定作為 JavaScript 變量嵌入
        var settings = {
            pageTitle: "<?php echo addslashes($settings['pageTitle']); ?>",
            lineGroup: "<?php echo addslashes($settings['lineGroup']); ?>"
        };
    </script>
</head>

<body>
    <h1><?php echo $settings['pageTitle']; ?></h1>
    <form id="registrationForm" action="upload.php" method="post">
        <div class="input-field">
            <input type="text" id="name" name="name" required>
            <label for="name">姓名</label>
        </div>
        <div class="input-field">
            <input type="text" id="phone" name="phone" pattern="09[0-9]{8}" required>
            <label for="phone">手機號碼</label>
        </div>
        <div class="input-field">
            <p>選擇的地區: <span id="confirmGroup"></span></p>
            <p>
                <label>
                    <input type="radio" name="group" value="宜蘭" />
                    <span>宜蘭</span>
                </label>
            </p>
            <p>
                <label>
                    <input type="radio" name="group" value="花蓮" />
                    <span>花蓮</span>
                </label>
            </p>
            <p>
                <label>
                    <input type="radio" name="group" value="臺東" />
                    <span>臺東</span>
                </label>
            </p>
        </div>

        <button class="btn waves-effect waves-light" type="button" onclick="showConfirmModal()">註冊 / 帳號查詢</button>
    </form>

    <div id="loadingModal">
        <div class="modal-content">
            <div class="modal-box">
                <div class="modal-message">正在處理...</div>
            </div>
        </div>
    </div>

    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <div id="confirmContent">
                <h5>以下是您填寫的資訊，請核對</h5>
                <p>姓名: <span id="confirmName"></span></p>
                <p>手機號碼: <span id="confirmPhone"></span></p>
                <p>合心: <span id="confirmGroup1"></span></p>
                
                <div class="credentials">
                    <p>您的帳密如下：</p>
                    <p>帳號: <span id="confirmUsername"></span></p>
                    <p>密碼: <span id="confirmPassword"></span></p>
                    <p>合心: <span id="confirmGroup2"></span></p>
                </div>
                
                <div class="line-instruction">
                    <p>點擊確認後務必加line官方好友，後續教學會使用line官方</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn waves-effect waves-light red lighten-2" onclick="hideConfirmModal();">返回</button> <!-- 修改這裡 -->
                <button class="btn waves-effect waves-light" onclick="submitForm()">確認</button> <!-- 修改這裡 -->
            </div>
        </div>
    </div>
    <div id="duplicateModal" class="modal">
        <div class="modal-content">
            <h5>帳號查詢</h5>
            <p>此名稱和電話號碼已註冊！</p>
            <p>姓名: <span id="duplicateName"></span></p> <!-- 用於顯示原始姓名 -->
            <p>手機號碼: <span id="duplicatePhone"></span></p> <!-- 用於顯示手機號碼 -->
            <p>帳密: <span id="duplicateID"></span></p> <!-- 用於顯示手機號碼 -->
            <div class="modal-footer">
                <button class="btn waves-effect waves-light" onclick="hideDuplicateModal()">關閉</button>
            </div>
        </div>
    </div>
    <div id="safariModal" class="modal">
        <div class="modal-content">
            <h5>完成註冊!</h5>
            <p>請點擊以下連結加入 LINE 官方好友:</p>
            <a class="line-button" href="https://line.me/R/ti/p/<?php echo $settings['lineGroup']; ?>" target="_blank">官方好友</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modalElems = document.querySelectorAll('.modal');
            var modalInstances = M.Modal.init(modalElems, {});
        });
      
        function isMobileDevice() {
            return (typeof window.orientation !== "undefined") || (navigator.userAgent.indexOf('IEMobile') !== -1);
        }
      
        function showConfirmModal() {
            const name = $("#name").val().trim();
            const phone = $("#phone").val();
            const selectedGroup = $("input[name='group']:checked").val();
            console.log("Selected group:", selectedGroup);
      
            if (!name) {
                alert("請填寫姓名。");
                return;
            }
      
            const phonePattern = /^09[0-9]{8}$/;
            if (!phonePattern.test(phone)) {
                alert("請正確填寫手機號碼，必須是09開始，總數10碼(含09)。");
                return;
            }

            if (!selectedGroup) {
                alert("請選擇您的所在地。");
                return;
            }
                
            $.post("check_duplicate.php", { phone: phone }, function(response) {
                if (response.isDuplicate) {
                    $("#duplicateName").text(response.originalName); // 顯示原始姓名
                    $("#duplicatePhone").text(phone); // 顯示手機號碼
                    $("#duplicateID").text("tc" + phone); // 顯示手機號碼
                    showDuplicateModal();
                } else {
                $("#confirmName").text(name);
                $("#confirmPhone").text(phone);
                $("#confirmUsername").text("tc" + phone);
                $("#confirmPassword").text("tc" + phone);
                $("#confirmGroup1").text(selectedGroup);
                $("#confirmGroup2").text(selectedGroup);


                var modalElem = $('#confirmModal');
                var modalInstance = M.Modal.getInstance(modalElem);
                modalInstance.open();
                }
            }, "json");
        }
      
        function showDuplicateModal() {
            var modalElem = $('#duplicateModal');
            var modalInstance = M.Modal.getInstance(modalElem);
            modalInstance.open();
        }
      
        function hideDuplicateModal() {
            var modalElem = $('#duplicateModal');
            var modalInstance = M.Modal.getInstance(modalElem);
            modalInstance.close();
        }
      
        function hideConfirmModal() {
            var modalElem = $('#confirmModal');
            var modalInstance = M.Modal.getInstance(modalElem);
            modalInstance.close();
        }
      
        function submitForm() {
            hideConfirmModal();
            $("#registrationForm").submit();
      
            $("#loadingModal").css("display", "flex");
            $("button").prop("disabled", true);
      
            const name = $("#name").val().trim();
            const phone = $("#phone").val();
            const ldapAccount = "tc" + phone;
      
            // 以下是模擬的 api 請求
            setTimeout(function() {
                const response = {
                    result: "success" // 範例值
                };
      
                $("#loadingModal").hide();
                $("button").prop("disabled", false);
      
                if (response.result === "success") {
                    const lineUrl = "https://line.me/R/ti/p/" + settings.lineGroup;
                    if (isMobileDevice()) {
                        window.location.href = "line://ti/p/" + settings.lineGroup;
                        showSafariModal();
                    } else {
                        window.open(lineUrl, '_self');
                    }
                } else {
                    alert("註冊失敗，請再試一次!");
                }
            }, 1000);
        }
      
        function showSafariModal() {
            var modalElem = $('#safariModal');
            var modalInstance = M.Modal.getInstance(modalElem);
            modalInstance.open();
        }
      
        function hideSafariModal() {
            var modalElem = $('#safariModal');
            var modalInstance = M.Modal.getInstance(modalElem);
            modalInstance.close();
        }
      </script>
      

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>

</html>
