@echo off
chcp 866 > nul
fc %1 %2 > %3
echo ok
